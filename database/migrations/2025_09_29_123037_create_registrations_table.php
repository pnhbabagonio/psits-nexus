<?php
// database/migrations/2025_09_29_000001_create_registrations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Foreign keys with proper indexing
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Status with all possible values
            $table->enum('status', ['registered', 'waitlisted', 'cancelled', 'attended'])->default('registered');

            // Timestamps
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();

            // Additional fields for enhanced functionality
            $table->text('notes')->nullable();
            $table->boolean('attended')->default(false);

            $table->timestamps();

            // Composite unique constraint to prevent duplicate active registrations
            $table->unique(['event_id', 'user_id']);

            // Indexes for better query performance
            $table->index(['event_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['registered_at']);
            $table->index(['cancelled_at']);
            $table->index(['status', 'registered_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
