<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\Notification;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the appropriate dashboard based on user type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->isWorker()) {
            return $this->workerDashboard();
        } elseif (auth()->user()->isClient()) {
            return $this->clientDashboard();
        }

        // Fallback if no specific user type
        return view('dashboard.general');
    }

    /**
     * Display worker dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function workerDashboard()
    {
        $user = auth()->user();

        $appliedJobs = JobApplication::where('worker_id', $user->id)
            ->with('job')
            ->latest()
            ->take(5)
            ->get();

        $activeJobs = JobApplication::where('worker_id', $user->id)
            ->where('status', 'accepted')
            ->whereHas('job', function ($query) {
                $query->where('status', 'in_progress');
            })
            ->with('job')
            ->latest()
            ->take(5)
            ->get();

        $completedJobs = JobApplication::where('worker_id', $user->id)
            ->whereHas('job', function ($query) {
                $query->where('status', 'completed');
            })
            ->with('job')
            ->latest()
            ->take(5)
            ->get();

        $newMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.worker', compact(
            'appliedJobs',
            'activeJobs',
            'completedJobs',
            'newMessages',
            'notifications'
        ));
    }

    /**
     * Display client dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function clientDashboard()
    {
        $user = auth()->user();

        $postedJobs = Job::where('client_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $inProgressJobs = Job::where('client_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->take(5)
            ->get();

        $completedJobs = Job::where('client_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $jobApplications = JobApplication::whereHas('job', function ($query) use ($user) {
            $query->where('client_id', $user->id);
        })
            ->where('status', 'pending')
            ->with(['job', 'worker'])
            ->latest()
            ->take(5)
            ->get();

        $newMessages = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.client', compact(
            'postedJobs',
            'inProgressJobs',
            'completedJobs',
            'jobApplications',
            'newMessages',
            'notifications'
        ));
    }
}
