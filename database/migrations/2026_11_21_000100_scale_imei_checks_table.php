<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imei_checks', function (Blueprint $table) {
            // Checks accept serial numbers up to 50 chars (devices.imei is 50); 15 truncated or failed them.
            $table->string('imei', 50)->change();

            // Exact-match history for one identifier, newest first.
            $table->index(['imei', 'created_at']);
            // Admin log pages order by newest; without this, every page sorts the whole table.
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('imei_checks', function (Blueprint $table) {
            $table->dropIndex(['imei', 'created_at']);
            $table->dropIndex(['created_at']);
            $table->string('imei', 15)->change();
        });
    }
};
