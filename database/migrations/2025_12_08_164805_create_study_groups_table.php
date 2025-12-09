<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('study_groups', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('subject');
        $table->string('level')->nullable(); // high school, university, etc.
        $table->text('description')->nullable();
        $table->unsignedBigInteger('creator_id'); // user who created the group
        $table->integer('max_members')->default(20);
        $table->timestamps();

        $table->foreign('creator_id')->references('id')->on('users')->cascadeOnDelete();
    });
}

};
