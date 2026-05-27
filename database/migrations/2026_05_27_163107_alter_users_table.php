<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_verified')->default(false)->after('phone');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->nullable()->after('is_verified');
            $table->decimal('balance', 15, 2)->default(0)->after('verification_status');
            $table->boolean('has_initial_deposit')->default(false)->after('balance');
            $table->timestamp('initial_deposit_confirmed_at')->nullable()->after('has_initial_deposit');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'is_verified',
                'verification_status',
                'balance',
                'has_initial_deposit',
                'initial_deposit_confirmed_at',
            ]);
        });
    }
};
