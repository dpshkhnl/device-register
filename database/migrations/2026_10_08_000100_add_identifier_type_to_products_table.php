<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 'imei' or 'serial': which identifier the registration form asks for.
            $table->string('identifier_type', 10)->default('imei')->after('slug');
        });

        DB::table('products')
            ->whereIn('slug', ['laptop', 'smartwatch', 'other'])
            ->update(['identifier_type' => 'serial']);

        // Serial numbers are stored in devices.imei too, and can be longer than 15 chars.
        Schema::table('devices', function (Blueprint $table) {
            $table->string('imei', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('identifier_type');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->string('imei', 15)->change();
        });
    }
};
