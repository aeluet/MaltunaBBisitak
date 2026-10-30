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
        Schema::create('visit_visit_type', function (Blueprint $table) {
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_type_id')->constrained()->restrictOnDelete();

            $table->primary(['visit_id', 'visit_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_visit_type');
    }
};
