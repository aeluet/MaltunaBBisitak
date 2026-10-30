<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visit_visitor', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->unique()->after('visitor_id');
        });

        // Attendances registered before this migration also get a check-in code (same alphabet as VisitAttendance).
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

        DB::table('visit_visitor')->whereNull('code')->lazyById()->each(function (object $attendance) use ($alphabet): void {
            $code = implode(array_map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)], range(1, 10)));

            DB::table('visit_visitor')->where('id', $attendance->id)->update(['code' => $code]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_visitor', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
