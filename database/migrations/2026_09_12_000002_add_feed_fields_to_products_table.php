<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Echtes GTIN, falls jemals vom Kunden geliefert. Bleibt null für
            // Markenprodukte (La Nordica, Invicta, FreePoint, Sannover) solange
            // keine echten Daten vorliegen — wird NICHT erfunden.
            $table->string('gtin')->nullable()->after('sku');

            // Gruppiert echte Varianten (z. B. Farb-/Grössenvarianten desselben
            // Ofenmodells, unterschiedliche Palettengrössen desselben Pellet-
            // Produkts) für g:item_group_id im Merchant-Center-Feed.
            $table->string('item_group_id')->nullable()->after('gtin');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['gtin', 'item_group_id']);
        });
    }
};
