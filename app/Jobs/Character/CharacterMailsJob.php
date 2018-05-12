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

        // Mails dont use pages.....
        if ($this->lastMailId) {
            $params['query'] = [
                'last_mail_id' => $this->lastMailId
            ];
        }

        // Fetch all mails before last_mail_id and compare them with mails we don't know about.
        $mails = $this->getClient()->invoke('/characters/' . $this->getId() . '/mail', $params)->get('result')->keyBy('mail_id');
        $mailIds = $mails->pluck('mail_id');
        $knownMails = CharacterMail::select('mail_id')->whereIn('mail_id', $mailIds)->get()->pluck('mail_id');
        foreach ($knownMails as $knownMail) {
            $mails->forget($knownMail);
        }

        // Pull all the recipients, taking into account mailing lists.
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

        // In order to fetch ALL a characters mail we must keep track of what mails we have fetched of theirs
        // as multiple characters can receive a mail
        $knownCharacterMails = CharacterFetchedMails::select('mail_id')->whereIn('mail_id', $mailIds)->where('character_id', $this->getId())->get()->pluck('mail_id');
        $unknownCharacterMails = $mailIds->diff($knownCharacterMails);
        $unknownCharacterMails = $unknownCharacterMails->map(function ($mail){
            return ['mail_id' => $mail, 'character_id' => $this->getId()];
        });

        // Add the updated at / created at.
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

        // Get all the characters, incase we need to update them to.
        $fromChars = $mails->where('from_type', 'character')->pluck('from');
        $recipChars = $recipients->where('recipient_type', 'character')->pluck('recipient_id');
        $chars = collect([]);
        foreach ($fromChars as $fromChar) {
            $chars->put($fromChar, $fromChar);
        }
        foreach ($recipChars as $recipChar) {
            $chars->put($recipChar, $recipChar);
        }

        // Finally store them in the DB
        \DB::transaction(function ($db) use ($mails, $recipients, $labels, $unknownCharacterMails){
            CharacterMail::insert($mails->toArray());
            CharacterMailRecipient::insert($recipients->toArray());
            if ($unknownCharacterMails->count()) {
                CharacterFetchedMails::insert($unknownCharacterMails->toArray());
            }
        });


        // If all the characters mails were not found, that means there are more
        if ($unknownCharacterMails->count() === $mailIds->count()) {
            dispatch(new CharacterMailsJob($this->token, $mailIds->last()));
        }

        // Get all the mail bodies.
        foreach ($mails->pluck('mail_id') as $mail) {
            dispatch( new CharacterMailJob($this->token, $mail) );
        }

        // And finally update characters as well.
        $knownChars = Character::select('character_id')->whereIn('character_id', $chars)->get()->pluck('character_id');
        $unknownChars = $chars->diff($knownChars);
        foreach ($unknownChars as $char) {
            dispatch(new CharacterUpdateJob($char));
        }

        $this->logFinished();
    }

}
