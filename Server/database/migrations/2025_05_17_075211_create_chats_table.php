<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->onDelete('cascade');
            $table->string('sender'); // 'Doctor' or patient name
            $table->text('text');
            $table->timestamp('timestamp');
            $table->timestamps();
        }
    );
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}