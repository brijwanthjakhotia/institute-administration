<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private Notification $notification;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_has_required_attributes()
    {
        $this->assertNotNull($this->notification->title);
        $this->assertNotNull($this->notification->content);
        $this->assertNotNull($this->notification->type);
        $this->assertNotNull($this->notification->priority);
        $this->assertNotNull($this->notification->notifiable_type);
        $this->assertNotNull($this->notification->notifiable_id);
    }

    /** @test */
    public function it_belongs_to_notifiable()
    {
        $this->assertInstanceOf(User::class, $this->notification->notifiable);
        $this->assertEquals($this->user->id, $this->notification->notifiable->id);
    }

    /** @test */
    public function it_can_format_type()
    {
        $this->notification->update(['type' => 'announcement']);
        $this->assertEquals('Announcement', $this->notification->formattedType());

        $this->notification->update(['type' => 'reminder']);
        $this->assertEquals('Reminder', $this->notification->formattedType());
    }

    /** @test */
    public function it_can_format_priority()
    {
        $this->notification->update(['priority' => 'high']);
        $this->assertEquals('High', $this->notification->formattedPriority());

        $this->notification->update(['priority' => 'low']);
        $this->assertEquals('Low', $this->notification->formattedPriority());
    }

    /** @test */
    public function it_can_format_status()
    {
        $this->notification->update(['status' => 'sent']);
        $this->assertEquals('Sent', $this->notification->formattedStatus());

        $this->notification->update(['status' => 'draft']);
        $this->assertEquals('Draft', $this->notification->formattedStatus());
    }

    /** @test */
    public function it_can_format_scheduled_date()
    {
        $date = now()->addDay();
        $this->notification->update(['scheduled_at' => $date]);
        $this->assertEquals($date->format('M d, Y H:i'), $this->notification->formattedScheduledDate());
    }

    /** @test */
    public function it_can_format_sent_date()
    {
        $date = now();
        $this->notification->update(['sent_at' => $date]);
        $this->assertEquals($date->format('M d, Y H:i'), $this->notification->formattedSentDate());
    }

    /** @test */
    public function it_can_check_if_scheduled()
    {
        $this->notification->update(['scheduled_at' => now()->addDay()]);
        $this->assertTrue($this->notification->isScheduled());

        $this->notification->update(['scheduled_at' => null]);
        $this->assertFalse($this->notification->isScheduled());
    }

    /** @test */
    public function it_can_check_if_sent()
    {
        $this->notification->update(['status' => 'sent', 'sent_at' => now()]);
        $this->assertTrue($this->notification->isSent());

        $this->notification->update(['status' => 'draft', 'sent_at' => null]);
        $this->assertFalse($this->notification->isSent());
    }

    /** @test */
    public function it_can_check_if_draft()
    {
        $this->notification->update(['status' => 'draft']);
        $this->assertTrue($this->notification->isDraft());

        $this->notification->update(['status' => 'sent']);
        $this->assertFalse($this->notification->isDraft());
    }
} 