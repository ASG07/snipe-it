<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('access_tag')->unique();
            $table->string('username')->nullable();
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->string('assigned_type')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('requestable')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('access');
    }
} 