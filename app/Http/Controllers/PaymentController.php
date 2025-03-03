<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Check if user is the client
        if ($user->id !== $job->client_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to make payments for this job.');
        }

        // Check if the job is already paid
        if ($job->payment) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'Payment for this job already exists.');
        }

        // Get the accepted application to determine the amount
        $application = $job->applications()->where('status', 'accepted')->first();

        if (!$application) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'There is no accepted worker for this job.');
        }

        // Create payment (in real app, you'd integrate with Stripe or another payment processor)
        $payment = Payment::create([
            'job_id' => $job->id,
            'amount' => $application->price_quoted ?? $job->budget,
            'status' => 'in_escrow',
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Payment has been placed in escrow.');
    }

    public function release(Payment $payment)
    {
        $user = auth()->user();
        $job = $payment->job;

        // Check if user is the client
        if ($user->id !== $job->client_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to release this payment.');
        }

        // Check if the payment is in escrow
        if ($payment->status !== 'in_escrow') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'This payment cannot be released.');
        }

        // Update payment status
        $payment->update(['status' => 'released']);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Payment has been released to the worker.');
    }
}
