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
        // If the jobs table exists (for queue), we'll rename it
        if (Schema::hasTable('jobs') && !Schema::hasTable('queue_jobs')) {
            Schema::rename('jobs', 'queue_jobs');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here since recreating the table would be handled by the original migration
    }
};
