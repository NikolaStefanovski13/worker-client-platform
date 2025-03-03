<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If the jobs table exists, we'll drop it since we're using work_jobs
        if (Schema::hasTable('jobs')) {
            Schema::drop('jobs');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here since recreating the table would be handled by the original migration
        // If needed, you could recreate the table here, but it's safer to let the original migration handle that
    }
};
