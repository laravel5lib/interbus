<?php

namespace App\Jobs\Character;

use App\Jobs\AuthenticatedESIJob;
use App\Models\Character\CharacterMail;
use App\Models\Token;

class CharacterMailsJob extends AuthenticatedESIJob
{

    protected $to;

    public function __construct(Token $token, int $to = 0)
    {
        parent::__construct($token);
        $this->to = $to;
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
        $mailIds = collect([]);

        $mails = $this->getClient()->invoke('/characters/' . $this->getId() . '/mail')->get('result');

        //TODO optimize this....
        while ( $mails->count() > 0 ){

            $mailIds = $mailIds->merge($mails->pluck('mail_id'));

            $latest = $mails->last()['mail_id'];
            if ($latest <= $this->to) {
                break;
            }

            $params['query'] = [
                'last_mail_id' => $latest
            ];

            $mails = $this->getClient()->invoke('/characters/' . $this->getId() . '/mail', $params)->get('result');
        }

        $knownMails = CharacterMail::select('mail_id')->whereIn('mail_id', $mailIds)->get()->pluck('mail_id');
        $unknownMails = $mailIds->diff($knownMails);

        foreach ($unknownMails as $mail) {
            dispatch( new CharacterMailJob($this->token, $mail) );
        }

        $this->logFinished();
    }

}
