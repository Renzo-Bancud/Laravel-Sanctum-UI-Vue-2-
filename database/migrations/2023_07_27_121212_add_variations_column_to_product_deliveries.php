<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariationsColumnToProductDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_deliveries', function (Blueprint $table) {
            $table->text('variation_id')->after('product_id');
            $table->text('variation_option_id')->after('variation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_deliveries', function (Blueprint $table) {
            $table->dropColumn('variation_id');
            $table->dropColumn('variation_option_id');
        });
    }
}
