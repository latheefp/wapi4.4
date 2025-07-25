<?php
namespace App\Service;

use Cake\Http\Client;
use Cake\Log\Log;
use Cake\Log\LogTrait;

class SlackService
{
    use LogTrait; // Include the LogTrait
    protected $webhookUrl;

    public function __construct()
    {
        // Set your Slack Webhook URL (from configuration or directly)
        $this->webhookUrl = getenv('SLACK_WEBHOOK_URL');
    }

    /**
     * Send message to Slack.
     *
     * @param string $message The message to send.
     * @param string $channel (optional) The Slack channel to send the message to.
     * @param string $username (optional) The username to display in Slack.
     * @return void
     */
    public function sendMessage($message, $channel = "wapi-alert", $username = 'WAJunction')
    {   
        $response=[];
        // Construct the payload to send
       /// debug($this->webhookUrl);
        $payload = [
            'text' => $message,
            'username' => $username,
        ];

        // Optionally add a channel
        if ($channel) {
            $payload['channel'] = $channel;
        }

        // Send the request using CakePHP's Http Client
        $http = new Client();
        $response = $http->post($this->webhookUrl, json_encode($payload), ['type' => 'json']);

        //debug($response);

        // if (!$response->isOk()) {
        //     throw new \Exception('Failed to send message to Slack');
        // }

        if (!$response->isOk()) {
            // Log the warning message
            $this->log('Warning: Failed to send message to Slack', 'warning');
            $this->log('Response: ' . $response->getBody(), 'warning');
            
            // Alternatively, display a user-friendly warning message
           $response="Failed to send message in slack";
        }

        return json_encode($response);
    }
}
