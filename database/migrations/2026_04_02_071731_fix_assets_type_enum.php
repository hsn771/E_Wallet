<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE assets MODIFY COLUMN type ENUM('cash', 'bank', 'property', 'investment', 'accounts_receivable', 'other')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE assets MODIFY COLUMN type ENUM('cash', 'bank', 'property', 'investment', 'other')");
    }
};
