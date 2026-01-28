<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('study_groups')->cascadeOnDelete();
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('meeting_link')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_sessions');
    }
};
