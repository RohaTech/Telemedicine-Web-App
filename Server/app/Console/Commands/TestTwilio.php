<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwilioService;

class TestTwilio extends Command
{
  protected $signature = 'twilio:test';
  protected $description = 'Test Twilio configuration';

  public function handle()
  {
    try {
      $this->info('Testing Twilio configuration...');
      $this->info('SID: ' . config('services.twilio.sid'));
      $this->info('Token: ' . (config('services.twilio.token') ? 'Set' : 'Not set'));
      $this->info('From: ' . config('services.twilio.from'));

      $twilio = new TwilioService();
      $this->info('Twilio service initialized successfully!');
    } catch (\Exception $e) {
      $this->error('Twilio configuration error: ' . $e->getMessage());
    }
  }
}
