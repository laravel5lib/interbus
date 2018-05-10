<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\Character;
use App\Models\Character\CharacterFetchedMails;
use App\Models\Character\CharacterMail;
use App\Models\Character\CharacterMailRecipient;
use App\Models\Token;
use Carbon\Carbon;

class CharacterMailsJob extends AuthenticatedESIJob
{

    protected $lastMailId;

    public function __construct(Token $token, int $lastMailId = 0)
    {
        parent::__construct($token);
        $this->lastMailId = $lastMailId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $params = [];

        if ($this->lastMailId) {
            $params['query'] = [
                'last_mail_id' => $this->lastMailId
            ];
        }

        $mails = $this->getClient()->invoke('/characters/' . $this->getId() . '/mail', $params)->get('result')->keyBy('mail_id');
        $mailIds = $mails->pluck('mail_id');
        $knownMails = CharacterMail::select('mail_id')->whereIn('mail_id', $mailIds)->get()->pluck('mail_id');
        foreach ($knownMails as $knownMail) {
            $mails->forget($knownMail);
        }

        $recipients = $mails->pluck('recipients', 'mail_id');
        $recipients = $recipients->map(function ($recipients, $key) use (&$mails){
            $returnRecips = [];
            foreach ($recipients as $recipient) {
                $from_type = 'character';
                if ($recipient['recipient_type'] === 'mailing_list' && $recipient['recipient_id'] === $mails[$key]['from']) {
                    $from_type = 'mailing_list';
                }
                $mails->put($key, array_merge($mails->get($key), ['from_type' => $from_type]));
                $returnRecips[] = array_merge($recipient, ['mail_id' => $key]);
            }
            return $returnRecips;
        });
        $recipients = $recipients->flatten(1);
        $labels = $mails->pluck('labels');

        $knownCharacterMails = CharacterFetchedMails::select('mail_id')->whereIn('mail_id', $mailIds)->where('character_id', $this->getId())->get()->pluck('mail_id');
        $unknownCharacterMails = $mailIds->diff($knownCharacterMails);
        $unknownCharacterMails = $unknownCharacterMails->map(function ($mail){
            return ['mail_id' => $mail, 'character_id' => $this->getId()];
        });

        $now = Carbon::now();
        $mails = $mails->map(function ($mail) use ($now){

            if (isset($mail['labels'])) {
                unset($mail['labels']);
            }
            if (isset($mail['recipients'])) {
                unset($mail['recipients']);
            }
            $mail['timestamp'] = Carbon::parse($mail['timestamp']);

            return array_merge($mail, ['updated_at' => $now, 'created_at' => $now]);
        });

        $fromChars = $mails->where('from_type', 'character')->pluck('from');
        $recipChars = $recipients->where('recipient_type', 'character')->pluck('recipient_id');
        $chars = collect([]);
        foreach ($fromChars as $fromChar) {
            $chars->put($fromChar, $fromChar);
        }
        foreach ($recipChars as $recipChar) {
            $chars->put($recipChar, $recipChar);
        }

        \DB::transaction(function ($db) use ($mails, $recipients, $labels, $unknownCharacterMails){
            CharacterMail::insert($mails->toArray());
            CharacterMailRecipient::insert($recipients->toArray());
            if ($unknownCharacterMails->count()) {
                CharacterFetchedMails::insert($unknownCharacterMails->toArray());
            }
        });


        if ($unknownCharacterMails->count() === $mailIds->count()) {
            dispatch(new CharacterMailsJob($this->token, $mailIds->last()));
        }

        foreach ($mails->pluck('mail_id') as $mail) {
            dispatch( new CharacterMailJob($this->token, $mail) );
        }

        $knownChars = Character::select('character_id')->whereIn('character_id', $chars)->get()->pluck('character_id');
        $unknownChars = $chars->diff($knownChars);
        foreach ($unknownChars as $char) {
            dispatch(new CharacterUpdateJob($char));
        }

        $this->logFinished();
    }

}
