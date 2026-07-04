<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: drop the check constraint and change to varchar to allow 'qr'
        DB::statement("ALTER TABLE pagos DROP CONSTRAINT IF EXISTS pagos_metodo_pago_check");
        DB::statement("ALTER TABLE pagos ALTER COLUMN metodo_pago TYPE VARCHAR(30)");
        DB::statement("UPDATE pagos SET metodo_pago = 'qr' WHERE metodo_pago NOT IN ('qr','transferencia','efectivo','tarjeta_credito','tarjeta_debito')");
    }

    public function down(): void
    {
        // Restore original enum (best-effort)
        DB::statement("ALTER TABLE pagos ALTER COLUMN metodo_pago TYPE VARCHAR(30)");
    }
};
