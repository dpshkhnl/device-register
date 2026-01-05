<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_areas', function (Blueprint $table) {
            $table->boolean('allow_email_login')->default(true)->after('is_active');
            $table->boolean('require_email_otp')->default(false)->after('allow_email_login');
            $table->boolean('allow_phone_login')->default(true)->after('require_email_otp');
            $table->boolean('require_phone_otp')->default(false)->after('allow_phone_login');
        });
    }

    public function down(): void
    {
        Schema::table('service_areas', function (Blueprint $table) {
            $table->dropColumn([
                'allow_email_login',
                'require_email_otp',
                'allow_phone_login',
                'require_phone_otp',
            ]);
        });
    }
};
