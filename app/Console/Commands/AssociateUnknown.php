<?php

namespace App\Console\Commands;

use App\Jobs\Alliance\AllianceUpdateJob;
use App\Jobs\Character\CharacterUpdateJob;
use App\Jobs\Corporation\CorporationUpdateJob;
use App\Jobs\Universe\UniverseGateJob;
use App\Models\Character\CharacterJournalEntry;
use App\Models\Character\CharacterMail;
use App\Models\Character\CharacterMailRecipient;
use App\Models\Universe\UniverseGate;
use Illuminate\Console\Command;

class AssociateUnknown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interbus:associate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Associates unknown corps, alliances, characters';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $senders = CharacterMail::where('from_type', 'character')->doesntHave('sender')->get();
        foreach ($senders as $sender) {
            dispatch(new CharacterUpdateJob($sender));
        }
        $this->info(count($senders) . ' Mail Senders Queued');

        return;

        //Yea so this doesn't work at all...
        $recipients = CharacterMailRecipient::whereNotIn('recipient_type', ['mailing_list'])->doesntHave('recipient')->get();
        $this->dispatchFromType($recipients, 'recipient');

        $firstParties = CharacterJournalEntry::whereNotNull('first_party_id')->doesntHave('firstParty')->get();
        $this->dispatchFromType($firstParties, 'first_party');

        $secondParties = CharacterJournalEntry::whereNotNull('second_party_id')->doesntHave('secondParty')->get();
        $this->dispatchFromType($secondParties, 'second_party');
    }

    public function dispatchFromType($types, string $key) {

        foreach ($types as $type) {
            $id = $type[$key . '_id'];
            switch ($type[$key .'_type']) {
                case 'character':
                    dispatch(new CharacterUpdateJob($id));
                    break;
                case 'corporation':
                    dispatch(new CorporationUpdateJob($id));
                    break;
                case 'alliance':
                    dispatch(new AllianceUpdateJob($id));
                    break;
            }
        }
    }
}
