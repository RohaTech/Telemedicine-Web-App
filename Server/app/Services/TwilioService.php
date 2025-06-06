<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
  protected $client;
  protected $from;
  public function __construct()
  {
    $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    $this->from = env('TWILIO_PHONE_NUMBER');
  }
  public function sendSMS($to, $message)
  {
    try {
      $this->client->messages->create($to, [
        'from' => $this->from,
        'body' => $message,
      ]);
      return ['status' => 'success', 'message' => 'SMS sent successfully'];
    } catch (\Exception $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }
}
