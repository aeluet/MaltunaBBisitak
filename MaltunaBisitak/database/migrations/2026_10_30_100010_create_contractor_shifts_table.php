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
        Schema::create('contractor_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->restrictOnDelete();
            $table->timestamp('checked_in_at')->index();
            $table->timestamp('checked_out_at')->nullable()->index();
            $table->boolean('needs_key')->default(false);
            $table->boolean('key_returned')->nullable();
            $table->string('signature_path')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_shifts');
    }
};
