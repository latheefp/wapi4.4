<?php
// src/Job/ProcessChatJob.php

namespace App\Job;

use Cake\Log\Log;
use Cake\Queue\Job\QueueJob;

class ProcessChatJob extends QueueJob
{
    public function perform(array $data)
    {
        // Your job logic here
        Log::debug('Running ProcessChat job with data: ' . json_encode($data));
        // Return true if successful, false if failed
        return true;
    }
}