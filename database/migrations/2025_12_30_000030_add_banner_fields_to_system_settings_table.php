<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('banner_text')->nullable()->after('contact_phone');
            $table->string('banner_link_label')->nullable()->after('banner_text');
            $table->string('banner_link_url')->nullable()->after('banner_link_label');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['banner_text', 'banner_link_label', 'banner_link_url']);
        });
    }
};
