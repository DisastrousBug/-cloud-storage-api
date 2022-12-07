<?php

use App\Models\Folder;
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

        if(!Schema::hasTable('files')){

            Schema::create('files', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('model'); //In case when not only User can access data (Company, Team, etc)
                $table->uuid('uuid')->nullable()->unique();
                $table->string('original_name');
                $table->string('file_name');
                $table->string('mime_type')->nullable();
                $table->string('extension')->nullable();
                $table->string('disk');
                $table->string('path');
                $table->foreignIdFor(Folder::class, 'folder_id')->nullable();
                $table->unsignedBigInteger('size');
                $table->unsignedInteger('order_column')->nullable()->index();
                $table->dateTime("delete_at")->nullable();
                $table->nullableTimestamps();
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
        Schema::dropIfExists('files');
    }
};
