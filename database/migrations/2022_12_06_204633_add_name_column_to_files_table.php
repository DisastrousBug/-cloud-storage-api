<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('files','name')){
            Schema::table('files', function (Blueprint $table) {
                $table->string('name')->default('noname');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('files','name')){
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
