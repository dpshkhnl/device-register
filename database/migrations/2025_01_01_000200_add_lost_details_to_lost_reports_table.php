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
        Schema::table('lost_reports', function (Blueprint $table) {
            $table->string('contact_phone_1', 20)->nullable()->after('description');
            $table->string('contact_phone_2', 20)->nullable()->after('contact_phone_1');
            $table->string('incident_type', 20)->nullable()->after('contact_phone_2');
            $table->date('incident_date')->nullable()->after('incident_type');
            $table->string('incident_location')->nullable()->after('incident_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_reports', function (Blueprint $table) {
            $table->dropColumn([
                'contact_phone_1',
                'contact_phone_2',
                'incident_type',
                'incident_date',
                'incident_location',
            ]);
        });
    }
};
