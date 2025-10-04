<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'requirement_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};