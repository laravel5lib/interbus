<?php

namespace App\Jobs\Character;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\AuthenticatedESIJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Character\CharacterMail;
use App\Models\Character\CharacterMailRecipient;
use App\Models\Corporation\Corporation;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CharacterMailJob extends AuthenticatedESIJob
{

    protected $mail;

    public function __construct(Token $token, int $mail)
    {
        parent::__construct($token);
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logStart();

        $mail = $this->getClient()->invoke('/characters/' . $this->getId() . '/mail/' . $this->mail)
            ->get('result');

        DB::transaction(function ($db) use ($mail) {
            $from_type = 'character';
            $recipients = collect($mail->pull('recipients'));
            $labels = $mail->pull('labels');

            $mail->put('timestamp', Carbon::parse($mail->get('timestamp')));
            //TODO make more efficient...
            foreach ($recipients as $recipient) {
                if ($recipient['recipient_type'] === 'mailing_list' && $recipient['recipient_id'] === $mail['from']) {
                    $from_type = 'mailing_list';
                }
                CharacterMailRecipient::updateOrCreate(['recipient_id' => 'recipient_id', 'mail_id' => $this->mail],
                    $recipient
                );
            }

            CharacterMail::updateOrCreate( ['mail_id' => $this->mail],
                array_merge($mail->toArray(), ['from_type' => $from_type] )
            );

            $charIds = $recipients->where('recipient_type', 'character')->pluck('recipient_id');
            $knownChars = Character::select('character_id')->whereIn('character_id', $charIds)->get()->pluck('character_id');
            $unknownChars = $charIds->diff($knownChars);
            foreach ($unknownChars as $char) {
                dispatch(new CharacterUpdateJob($char));
            }

        });


        $this->logFinished();
    }

}
