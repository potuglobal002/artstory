<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    public function test_contact_message_tracks_its_contact_lifecycle(): void
    {
        $message = new ContactMessage([
            'name' => 'Niloy Saha',
            'email' => 'niloy@example.com',
            'subject' => 'Gallery visit',
            'message' => 'I would like to arrange a visit.',
        ]);

        $message->markStatus('contacted');

        $this->assertSame('contacted', $message->status);
        $this->assertNotNull($message->contacted_at);
        $this->assertNull($message->closed_at);

        $message->markStatus('closed');

        $this->assertSame('closed', $message->status);
        $this->assertNotNull($message->closed_at);
    }
}
