<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;

class TwilioService
{
  protected $client;
  protected $from;

  public function __construct()
  {
    $sid = config('services.twilio.sid');
    $token = config('services.twilio.token');
    $this->from = config('services.twilio.from');

    if (!$sid || !$token || !$this->from) {
      throw new Exception('Twilio credentials are not properly configured');
    }

    $this->client = new Client($sid, $token);
  }

  public function sendSMS($to, $message)
  {
    try {
      $result = $this->client->messages->create($to, [
        'from' => $this->from,
        'body' => $message,
      ]);

      return [
        'success' => true,
        'message' => 'SMS sent successfully',
        'message_sid' => $result->sid,
        'status' => $result->status
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }
}
