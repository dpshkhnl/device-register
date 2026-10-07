<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_age_options', function (Blueprint $table) {
            $table->id();
            $table->string('label', 60)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaults = [
            'Less than 1 month',
            '1-3 months',
            '3-6 months',
            '6-12 months',
            '1-2 years',
            'More than 2 years',
        ];

        DB::table('device_age_options')->insert(array_map(fn ($label, $index) => [
            'label' => $label,
            'sort_order' => $index,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], $defaults, array_keys($defaults)));

        // Second-hand devices record an age range instead of an exact purchase date.
        Schema::table('devices', function (Blueprint $table) {
            $table->date('purchase_date')->nullable()->change();
            $table->string('device_age', 60)->nullable()->after('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('device_age');
        });

        Schema::dropIfExists('device_age_options');
    }
};
