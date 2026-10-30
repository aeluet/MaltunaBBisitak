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
        Schema::create('visit_visitor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->constrained()->restrictOnDelete();
            $table->timestamp('checked_in_at')->nullable()->index();
            $table->timestamp('checked_out_at')->nullable();
            $table->string('badge_number', 20)->nullable();
            $table->timestamp('label_printed_at')->nullable();
            $table->timestamps();

            $table->unique(['visit_id', 'visitor_id']);
            $table->index(['checked_out_at', 'checked_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_visitor');
    }
};
