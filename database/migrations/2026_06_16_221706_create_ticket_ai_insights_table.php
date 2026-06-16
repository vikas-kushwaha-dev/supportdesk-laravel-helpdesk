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
        Schema::create('ticket_ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();

            $table->enum('suggested_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->string('suggested_category')->nullable();
            $table->string('sentiment')->nullable();

            $table->text('summary')->nullable();
            $table->text('recommended_action')->nullable();

            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('ai_model')->nullable();

            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_ai_insights');
    }
};
