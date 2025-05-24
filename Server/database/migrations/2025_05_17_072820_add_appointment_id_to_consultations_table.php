<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentIdToConsultationsTable extends Migration
{
    public function up()
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropColumn('appointment_id');
        });
    }
}