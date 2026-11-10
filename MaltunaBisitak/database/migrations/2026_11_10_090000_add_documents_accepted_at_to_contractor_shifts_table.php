<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When the contractor confirmed having read the safety documents before signing.
     */
    public function up(): void
    {
        Schema::table('contractor_shifts', function (Blueprint $table) {
            $table->timestamp('documents_accepted_at')->nullable()->after('signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('contractor_shifts', function (Blueprint $table) {
            $table->dropColumn('documents_accepted_at');
        });
    }
};
