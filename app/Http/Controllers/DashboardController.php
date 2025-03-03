php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['worker', 'client'])->default('client');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
if ($user->role === 'worker') {
    // Get worker's applied jobs
    $appliedJobs = JobApplication::where('worker_id', $user->id)
        ->with('job')
        ->get()
        ->groupBy('status');

    // Get jobs that match worker's skills
    $skillIds = $user->skills->pluck('id')->toArray() ?? [0];
    $matchingJobs = Job::where('status', 'posted')
        ->whereHas('category', function ($query) use ($skillIds) {
            $query->whereIn('id', $skillIds);
        })
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard.worker', compact('appliedJobs', 'matchingJobs'));
} else {
    // Get client's posted jobs
    $postedJobs = Job::where('client_id', $user->id)
        ->with(['applications' => function ($query) {
            $query->with('worker');
        }])
        ->latest()
        ->get()
        ->groupBy('status');

    return view('dashboard.client', compact('postedJobs'));
}
