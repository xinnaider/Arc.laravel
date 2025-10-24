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
        Schema::create('arc_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('jobable_type');
            $table->string('status')->default('pending');
            $table->json('details')->nullable();
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->string('trace')->nullable();
            $table->foreignId('child_job_id')->nullable()->constrained('arc_jobs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arc_jobs');
    }
};
