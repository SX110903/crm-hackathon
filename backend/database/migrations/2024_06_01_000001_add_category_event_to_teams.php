<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->enum('category', ['red', 'blue', 'general'])
                  ->default('general')
                  ->after('name');
            $table->foreignId('event_id')
                  ->nullable()
                  ->after('category')
                  ->constrained('events')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn(['category', 'event_id']);
        });
    }
};
