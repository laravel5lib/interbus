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
            $recipients = collect($mail->pull('recipients'));
            $labels = $mail->pull('labels');

            $mail->put('timestamp', Carbon::parse($mail->get('timestmap')));

            CharacterMail::updateOrCreate( ['mail_id' => $this->mail] ,
                $mail->toArray()
            );
            //$characterMail->recipients()->createMany($recipients);
            foreach ($recipients as $recipient) {
                CharacterMailRecipient::updateOrCreate(['recipient_id' => 'recipient_id', 'mail_id' => $this->mail],
                    $recipient
                );
            }

            $charIds = $recipients->where('recipient_type', 'character')->pluck('recipient_id');
            $knownChars = Character::select('character_id')->whereIn('character_id', $charIds)->get()->pluck('character_id');
            $unknownChars = $charIds->diff($knownChars);
            foreach ($unknownChars as $char) {
                dispatch(new CharacterUpdateJob($char));
            }

            $corpIds = $recipients->where('recipient_type', 'corporation')->pluck('recipient_id');
            $knownCorps = Corporation::select('corporation_id')->whereIn('corporation_id_id', $corpIds)->get()->pluck('corporation_id');
            $unknownCorps = $charIds->diff($knownCorps);
            foreach ($unknownCorps as $corp) {
                dispatch(new CorporationUpdateJob($corp));
            }

            $allianceIds = $recipients->where('recipient_type', 'alliance')->pluck('recipient_id');
            $knownAlliances = Alliance::select('alliance_id')->whereIn('alliance_id', $allianceIds)->get()->pluck('alliance_id');
            $unknownAlliances = $charIds->diff($knownAlliances);
            foreach ($unknownAlliances as $alliance) {
                dispatch(new AllianceUpdateJob($alliance));
            }

        });


        $this->logFinished();
    }

}
