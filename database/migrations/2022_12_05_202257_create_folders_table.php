<?php

use App\Models\User;
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
        if(!Schema::hasTable('folders')){
            Schema::create('folders', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('name');
                $table->foreignIdFor(User::class, 'user_id');
                $table->unsignedBigInteger('size')->nullable();
                $table->string('path');

                $table->unique(['name', 'user_id']);
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
        Schema::dropIfExists('folders');
    }
};
