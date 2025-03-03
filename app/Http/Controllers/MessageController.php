<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Get all jobs where the user has messages
        $jobsWithMessages = Job::whereHas('messages', function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->with(['client', 'applications' => function ($query) {
                $query->where('status', 'accepted')->with('worker');
            }])
            ->latest()
            ->get();

        return view('messages.index', compact('jobsWithMessages'));
    }

    public function show(Job $job)
    {
        $user = auth()->user();

        // Check if the user is authorized to view this job's messages
        if (
            $user->id !== $job->client_id &&
            !$job->applications()->where('worker_id', $user->id)->where('status', 'accepted')->exists()
        ) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view these messages.');
        }

        // Load the job with its client and accepted worker
        $job->load(['client', 'applications' => function ($query) {
            $query->where('status', 'accepted')->with('worker');
        }]);

        // Determine the other user in the conversation
        $otherUser = $user->id === $job->client_id
            ? $job->applications->first()->worker ?? null
            : $job->client;

        // Get all messages for this job
        $messages = Message::where('job_id', $job->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        // Mark unread messages as read
        Message::where('job_id', $job->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('job', 'messages', 'otherUser'));
    }

    public function store(Request $request, Job $job)
    {
        $user = auth()->user();

        // Check if the user is authorized to send messages for this job
        if (
            $user->id !== $job->client_id &&
            !$job->applications()->where('worker_id', $user->id)->where('status', 'accepted')->exists()
        ) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to send messages for this job.');
        }

        // Validate the request
        $request->validate([
            'content' => 'required|string',
        ]);

        // Determine the receiver
        $receiverId = $user->id === $job->client_id
            ? $job->applications()->where('status', 'accepted')->first()->worker_id
            : $job->client_id;

        // Create the message
        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'job_id' => $job->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }
}
