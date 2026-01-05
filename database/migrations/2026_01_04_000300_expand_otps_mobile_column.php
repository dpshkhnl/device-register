<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('otps')) {
            DB::statement('ALTER TABLE otps MODIFY mobile VARCHAR(255)');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('otps')) {
            DB::statement('ALTER TABLE otps MODIFY mobile VARCHAR(20)');
        }
    }
};
