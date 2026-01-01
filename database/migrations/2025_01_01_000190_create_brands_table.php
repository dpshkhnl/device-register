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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaults = [
            'Apple',
            'Samsung',
            'Google',
            'OnePlus',
            'Xiaomi',
            'Huawei',
            'Oppo',
            'Vivo',
            'Realme',
            'Motorola',
            'Nokia',
            'Sony',
            'LG',
            'Asus',
            'Lenovo',
            'Other',
        ];

        $rows = [];
        foreach ($defaults as $index => $name) {
            $rows[] = [
                'name' => $name,
                'sort_order' => $index,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('brands')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
