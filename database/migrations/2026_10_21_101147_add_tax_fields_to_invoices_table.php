<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Tax columns were already added by the earlier migration.
    }

    public function down(): void
    {
        // Do not remove columns owned by the earlier migration.
    }
};
