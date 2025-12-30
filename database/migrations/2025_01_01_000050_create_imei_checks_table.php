<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imei_checks', function (Blueprint $table) {
            $table->id();
            $table->string('imei', 15);
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users');
            $table->string('channel', 20);
            $table->string('result', 20);
            $table->string('status_returned', 32)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imei_checks');
    }
};
