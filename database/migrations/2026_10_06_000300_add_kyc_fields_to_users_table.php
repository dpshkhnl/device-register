<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('alternate_mobile', 20)->nullable()->after('mobile');
            $table->string('kyc_status', 20)->default('not_submitted')->index();
            $table->string('kyc_photo_path')->nullable();
            $table->string('kyc_id_type', 40)->nullable();
            $table->string('kyc_id_number', 60)->nullable();
            $table->string('kyc_id_front_path')->nullable();
            $table->string('kyc_id_back_path')->nullable();
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->timestamp('kyc_reviewed_at')->nullable();
            $table->foreignId('kyc_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kyc_rejection_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kyc_reviewed_by');
            $table->dropIndex(['kyc_status']);
            $table->dropColumn([
                'alternate_mobile',
                'kyc_status',
                'kyc_photo_path',
                'kyc_id_type',
                'kyc_id_number',
                'kyc_id_front_path',
                'kyc_id_back_path',
                'kyc_submitted_at',
                'kyc_reviewed_at',
                'kyc_rejection_reason',
            ]);
        });
    }
};
