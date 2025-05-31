<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['tuition', 'transportation', 'library', 'laboratory', 'sports', 'other']);
            $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'yearly']);
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_room_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_mandatory')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fees');
    }
}; 