<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('class_teacher', 'class_room_teacher');
    }

    public function down()
    {
        Schema::rename('class_room_teacher', 'class_teacher');
    }
}; 