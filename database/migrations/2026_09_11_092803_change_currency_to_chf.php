<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('products')->where('currency', 'EUR')->update(['currency' => 'CHF']);
        DB::table('orders')->where('currency', 'EUR')->update(['currency' => 'CHF']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')->where('currency', 'CHF')->update(['currency' => 'EUR']);
        DB::table('orders')->where('currency', 'CHF')->update(['currency' => 'EUR']);
    }
};
