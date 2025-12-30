<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->index();
            $table->foreignId('from_user_id')->constrained('users');
            $table->string('to_mobile', 20);
            $table->foreignId('to_user_id')->nullable()->constrained('users');
            $table->string('status', 20)->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
