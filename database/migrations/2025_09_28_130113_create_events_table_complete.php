<?php
// database/migrations/2025_09_28_000001_create_events_table_complete.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the old table if it exists
        Schema::dropIfExists('events');

        // Create the complete table
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->string('venue');
            $table->string('address');
            $table->enum('status', ['Upcoming', 'Ongoing', 'Completed'])->default('Upcoming');
            $table->integer('registered')->default(0);
            $table->integer('max_capacity');
            $table->string('organizer');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
