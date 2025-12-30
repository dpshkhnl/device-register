<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('DRMS');
            $table->string('brand_primary')->default('#2563EB');
            $table->string('brand_secondary')->default('#3B82F6');
            $table->string('hero_title')->default('Secure Your Mobile Devices');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_primary_label')->default('Verify IMEI Number');
            $table->string('hero_primary_url')->default('/imei');
            $table->string('hero_secondary_label')->default('Register Device');
            $table->string('hero_secondary_url')->default('/devices/create');
            $table->string('cta_title')->default('Ready to Protect Your Mobile Devices?');
            $table->string('cta_subtitle')->nullable();
            $table->string('cta_primary_label')->default('Register Device');
            $table->string('cta_primary_url')->default('/devices/create');
            $table->string('cta_secondary_label')->default('Sign Up Now');
            $table->string('cta_secondary_url')->default('/register');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
