<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterMail;
use App\Models\Character\CharacterMailRecipient;
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
            $recipients = $mail->pull('recipients');
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
        });


        $this->logFinished();
    }

}
