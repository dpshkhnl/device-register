<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('sms_provider', 40)->nullable()->default('aakashsms')->after('contact_phone');
            $table->string('sms_api_url')->nullable()->after('sms_provider');
            $table->string('sms_token')->nullable()->after('sms_api_url');
            $table->string('sms_sender', 60)->nullable()->after('sms_token');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['sms_provider', 'sms_api_url', 'sms_token', 'sms_sender']);
        });
    }
};
