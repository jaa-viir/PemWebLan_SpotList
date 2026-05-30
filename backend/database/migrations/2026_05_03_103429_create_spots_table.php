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
        Schema::create('spots', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('thumbnail');
            $table->text('description');
            $table->string('category');
            $table->string('location');
            $table->dateTime('event_date');
            $table->string('organizer');
            $table->integer('price')->default(0);
            $table->integer('participant_limit')->nullable();
            $table->string('registration_link')->nullable();
            $table->enum('status', ['open', 'closed', 'draft'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spots');
    }
};
