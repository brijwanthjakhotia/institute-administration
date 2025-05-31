<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('notifiable')->get();
        return view('notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('notifications.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:announcement,alert,reminder,payment_due,attendance,grade',
            'priority' => 'required|in:low,medium,high',
            'notifiable_type' => 'required|string',
            'notifiable_id' => 'required|integer',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification = Notification::create($request->all());

        if (!$request->scheduled_at) {
            // Send notification immediately if not scheduled
            $this->sendNotification($notification);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification)
    {
        $notification->load('notifiable');
        return view('notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        return view('notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:announcement,alert,reminder,payment_due,attendance,grade',
            'priority' => 'required|in:low,medium,high',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification->update($request->all());

        if (!$request->scheduled_at && $notification->status === 'draft') {
            // Send notification if it was a draft and not scheduled
            $this->sendNotification($notification);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    public function send(Notification $notification)
    {
        $this->sendNotification($notification);
        return redirect()->route('notifications.index')
            ->with('success', 'Notification sent successfully.');
    }

    protected function sendNotification(Notification $notification)
    {
        // Implement notification sending logic here
        // This could include:
        // - Sending emails
        // - Sending SMS
        // - Sending push notifications
        // - Updating notification status
        
        $notification->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
} 