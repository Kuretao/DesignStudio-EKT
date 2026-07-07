<?php

namespace Tests\Feature;

use App\Mail\LeadCreated;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_request_sends_notification_email(): void
    {
        Mail::fake();
        config(['mail.leads.to' => 'info@3dsmartdesign.ru']);

        $response = $this->postJson('/api/v1/leads', [
            'source' => 'site',
            'channel' => 'modal',
            'name' => 'Екатерина',
            'contact' => '+7 900 000-00-00',
            'service' => 'Дизайн интерьера',
            'message' => 'Хочу обсудить проект',
            'payload' => ['budget' => 'до 1 млн'],
        ]);

        $response->assertCreated()
            ->assertJson(['status' => 'ok']);

        $lead = Lead::query()->firstOrFail();

        Mail::assertSent(LeadCreated::class, function (LeadCreated $mail) use ($lead) {
            return $mail->lead->is($lead)
                && $mail->hasTo('info@3dsmartdesign.ru');
        });
    }
}
