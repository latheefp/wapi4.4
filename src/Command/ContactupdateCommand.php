<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Contactupdate command.
 */
class ContactupdateCommand extends Command {

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io) {

        $this->updatesteacontact();
    }

    function updatesteacontact() {
        $streams = $this->getTableLocator()->get('Streams')->find()->select(['id', 'contact_waid', 'contacts_profile_name'])->all();
        debug(count($streams));
        foreach ($streams as $key => $val) {

            print($val->id . "\n");
            $contactinfo = $this->getTableLocator()->get('ContactNumbers')->find()->where(['mobile_number' => $val->contact_waid])->first();
            //    debug($contactinfo);
            $contact_stream_table = $this->getTableLocator()->get('ContactStreams');
            $existing = $this->getTableLocator()->get('ContactStreams')->find()->where(['contact_number' => $val->contact_waid])->toArray();

            if (empty($existing)) {
                $row = $contact_stream_table->newEmptyEntity();
                if (isset($val->contacts_profile_name)) {
                    debug("Found $val->contacts_profile_name");
                    $row->profile_name = $val->contacts_profile_name;
                }
                $row->contact_number = $val->contact_waid;
                if (!empty($contactinfo->name)) {
                    $row->name = $contactinfo->name;
                }
                if ($contact_stream_table->save($row)) {
                    $contact_stream_id = $row->id;
                    $streamsrow = $this->getTableLocator()->get('Streams')->get($val->id);
                    $streamsrow->contact_stream_id = $contact_stream_id;
                    if ($this->getTableLocator()->get('Streams')->save($streamsrow)) {
                        //      debug("Streams table updated with  streams_contact_id ".$val->id);
                    } else {
                        //    debug("Streams table updated failed " .$val->id);
                    }
                } else {
                    // debug($row->getErrors());
                }
            } else {
                //  debug("Updating..." . $val->contact_waid);
                $row = $contact_stream_table->get($existing[0]->id);
                if(isset($val->contacts_profile_name)) {
                    debug("Found $val->contacts_profile_name");
                    $row->profile_name = $val->contacts_profile_name;
                }
                $row->contact_number = $val->contact_waid;
                if(!empty($contactinfo->name)) {
                    $row->name = $contactinfo->name;
                }
                $contact_stream_table->save($row);
                $streamsrow = $this->getTableLocator()->get('Streams')->get($val->id);
                $streamsrow->contact_stream_id = $row->id;
                //   debug($streamsrow);
                if ($this->getTableLocator()->get('Streams')->save($streamsrow)) {
                    //  debug("Streams table updated with  streams_contact_id of existing ".$val->id);
                } else {
                    // debug("Streams table update faield with existsing.".$val->id);
                }
            }
        }
        debug($val->id);
    }

}
