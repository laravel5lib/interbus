<?php

namespace App\Jobs\Character;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\AuthenticatedESIJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Models\Alliance\Alliance;
use App\Models\Character\Character;
use App\Models\Character\CharacterMail;
use App\Models\Character\CharacterMailBody;
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

        CharacterMailBody::updateOrCreate( ['mail_id' => $this->mail],
            ['body' => $mail['body']]
        );

        $this->logFinished();
    }

}
