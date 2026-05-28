<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('id_verifications', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('id_number');
            $table->string('place_of_birth')->nullable()->after('full_name');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->string('phone_number', 20)->nullable()->after('date_of_birth');
            $table->string('occupation')->nullable()->after('phone_number');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('occupation');
            $table->string('province')->nullable()->after('marital_status');
            $table->string('kabupaten')->nullable()->after('province');
            $table->string('kecamatan')->nullable()->after('kabupaten');
            $table->text('address')->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('id_verifications', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'place_of_birth', 'date_of_birth', 'phone_number',
                'occupation', 'marital_status', 'province', 'kabupaten', 'kecamatan', 'address',
            ]);
        });
    }
};
