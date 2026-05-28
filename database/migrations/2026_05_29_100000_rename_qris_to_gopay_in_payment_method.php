<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing data first
        DB::statement("UPDATE transactions SET payment_method = 'gopay' WHERE payment_method = 'qris'");
        DB::statement("UPDATE investments SET payment_method = 'gopay' WHERE payment_method = 'qris'");
        DB::statement("UPDATE installment_payments SET payment_method = 'gopay' WHERE payment_method = 'qris'");

        // Alter enum columns
        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('manual_transfer', 'gopay') NULL");
        DB::statement("ALTER TABLE investments MODIFY COLUMN payment_method ENUM('manual_transfer', 'gopay') NOT NULL DEFAULT 'manual_transfer'");
        DB::statement("ALTER TABLE installment_payments MODIFY COLUMN payment_method ENUM('manual_transfer', 'gopay') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE transactions SET payment_method = 'qris' WHERE payment_method = 'gopay'");
        DB::statement("UPDATE investments SET payment_method = 'qris' WHERE payment_method = 'gopay'");
        DB::statement("UPDATE installment_payments SET payment_method = 'qris' WHERE payment_method = 'gopay'");

        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('manual_transfer', 'qris') NULL");
        DB::statement("ALTER TABLE investments MODIFY COLUMN payment_method ENUM('manual_transfer', 'qris') NOT NULL DEFAULT 'manual_transfer'");
        DB::statement("ALTER TABLE installment_payments MODIFY COLUMN payment_method ENUM('manual_transfer', 'qris') NULL");
    }
};
