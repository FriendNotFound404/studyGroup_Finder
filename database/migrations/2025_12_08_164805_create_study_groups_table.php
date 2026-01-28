<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('subject');
            $table->string('level')->nullable(); // 1st year, 2nd year, etc.
            $table->text('description')->nullable();

            $table->enum('status', ['open', 'closed', 'archived'])
                  ->default('open');

            $table->unsignedBigInteger('creator_id');

            $table->integer('max_members')
                  ->default(20);

            $table->timestamps();

            $table->foreign('creator_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('study_groups');
    }
};
