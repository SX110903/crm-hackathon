<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('judges')->cascadeOnDelete();
            $table->decimal('innovation_score', 4, 2);
            $table->decimal('technical_score', 4, 2);
            $table->decimal('presentation_score', 4, 2);
            $table->decimal('usability_score', 4, 2);
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'judge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
