<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('name');
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->text("address_line_1")->nullable();
            $table->text("address_line_2")->nullable();
            $table->text("admin_area_1")->nullable();
            $table->text("admin_area_2")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("country_code")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->string('name');
            $table->dropColumn("first_name");
            $table->dropColumn("last_name");
            $table->dropColumn("address_line_1");
            $table->dropColumn("address_line_2");
            $table->dropColumn("admin_area_1");
            $table->dropColumn("admin_area_2");
            $table->dropColumn("postal_code");
            $table->dropColumn("country_code");
        });



    }
}
