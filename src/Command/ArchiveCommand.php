<?php
//bin/cake Archive -f cleanq
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime; // Import FrozenTime
use App\Controller\AppController; //(path to your controller).
use App\Service\SlackService;

class ArchiveCommand extends Command
{

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->addOptions(
            [
                'function' => [
                    'short' => 'f',
                    'help' => 'The function name',
                    'required' => true,
                ],
                'contact_csv' => [
                    'short' => 'o',
                    'help' => 'Options',
                    'required' => false,
                ]
            ]
        );

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        print("running Function => " . $args->getOption('function'). "\n");
        $function = $args->getOption('function');
        
        $this->app->writelog("Running Archive function ".$function);
        $this->$function();
    }


    public function initialize(): void
    {
        parent::initialize();
        $this->app = new AppController();
    }

    

    function cleanq(){
        $this->app = new AppController();
        //cleanSendQ

        $retentions = (int) $this->app->_getsettings('q_retention');
        debug("Deleting Send Q older than $retentions");

        $sendTable = $this->getTableLocator()->get('SendQueues');
        $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);

        $query = $sendTable->query();
        $sendCount = $query->select(['count' => $query->func()->count('*')])
            ->where([
                'http_response_code' => 200,
                'created <' => $retentionsHoursAgo
            ])
            ->execute()
            ->fetch('assoc')['count'];

        debug("Number of SendQ records to be deleted: $sendCount");

        $slackService = new SlackService();
        $slackService->sendMessage("Number of SendQ records to be deleted:". $sendCount);

        $query = $sendTable->query();
        $query->delete()
            ->where([
                'http_response_code' => 200,
                'created <' => $retentionsHoursAgo
            ])
            ->execute();

        debug("Deleting Send Q older than $retentions");

        $rcvTable = $this->getTableLocator()->get('RcvQueues');
        $retentionsHoursAgo = FrozenTime::now()->subHours($retentions);

        $query = $rcvTable->query();
        $rcvCount = $query->select(['count' => $query->func()->count('*')])
            ->where([
                'http_response_code' => 200,
                'created <' => $retentionsHoursAgo
            ])
            ->execute()
            ->fetch('assoc')['count'];

        debug("Number of RcvQ records to be deleted: $rcvCount");
        $slackService->sendMessage("Number of RcvQ records to be deleted:". $rcvCount);
        $query = $rcvTable->query();
        $query->delete()
            ->where([
                'http_response_code' => 200,
                'created <' => $retentionsHoursAgo
            ])
            ->execute();
        
     }
 


     public function cleanq3months()
        {
            $this->app = new AppController();

            $threeMonthsAgo = FrozenTime::now()->subMonths(3);

            $sendTable = $this->getTableLocator()->get('SendQueues');
            $rcvTable = $this->getTableLocator()->get('RcvQueues');

            // Count and delete SendQueues older than 3 months
            $query = $sendTable->query();
            $sendCount = $query->select(['count' => $query->func()->count('*')])
                ->where([
                    'created <' => $threeMonthsAgo
                ])
                ->execute()
                ->fetch('assoc')['count'];

            debug("Number of SendQ records older than 3 months to be deleted: $sendCount");

            $slackService = new SlackService();
            $slackService->sendMessage("Deleting $sendCount records from SendQueues older than 3 months");

            $query = $sendTable->query();
            $query->delete()
                ->where([
                    'created <' => $threeMonthsAgo
                ])
                ->execute();

            // Count and delete RcvQueues older than 3 months
            $query = $rcvTable->query();
            $rcvCount = $query->select(['count' => $query->func()->count('*')])
                ->where([
                    'created <' => $threeMonthsAgo
                ])
                ->execute()
                ->fetch('assoc')['count'];

            debug("Number of RcvQ records older than 3 months to be deleted: $rcvCount");
            $slackService->sendMessage("Deleting $rcvCount records from RcvQueues older than 3 months");

            $query = $rcvTable->query();
            $query->delete()
                ->where([
                    'created <' => $threeMonthsAgo
                ])
                ->execute();
        }

}
