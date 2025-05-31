<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_notifications()
    {
        Notification::factory()->count(3)->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'type',
                        'priority',
                        'status',
                        'scheduled_at',
                        'sent_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_notification()
    {
        $notificationData = [
            'title' => 'Test Notification',
            'content' => 'This is a test notification',
            'type' => 'announcement',
            'priority' => 'high',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s')
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/notifications', $notificationData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => $notificationData['title'],
                    'content' => $notificationData['content'],
                    'type' => $notificationData['type'],
                    'priority' => $notificationData['priority'],
                    'status' => 'scheduled'
                ]
            ]);

        $this->assertDatabaseHas('notifications', [
            'title' => $notificationData['title'],
            'content' => $notificationData['content'],
            'type' => $notificationData['type']
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_notification()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/notifications', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'type', 'priority', 'notifiable_type', 'notifiable_id']);
    }

    /** @test */
    public function it_can_update_a_notification()
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Notification',
            'content' => 'This is an updated notification',
            'type' => 'reminder',
            'priority' => 'medium'
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/notifications/{$notification->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => $updateData['title'],
                    'content' => $updateData['content'],
                    'type' => $updateData['type'],
                    'priority' => $updateData['priority']
                ]
            ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'title' => $updateData['title'],
            'content' => $updateData['content']
        ]);
    }

    /** @test */
    public function it_can_delete_a_notification()
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id
        ]);
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/notifications/{$notification->id}/send");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'sent'
                ]
            ]);

        $this->assertEquals('sent', $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->sent_at);
    }

    /** @test */
    public function it_can_show_notification_details()
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'status' => $notification->status
                ]
            ]);
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'status' => 'sent'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/notifications/{$notification->id}/mark-as-read");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'read'
                ]
            ]);

        $this->assertEquals('read', $notification->fresh()->status);
    }
} 