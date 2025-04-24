<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('medical_license_number')->unique();
            $table->string('specialty');
            $table->text('qualification');
            $table->integer('experience_years')->unsigned();
            $table->string('university_attended');
            $table->date('license_issue_date');
            $table->date('license_expiry_date');
            $table->enum('status', ['pending', 'active', 'suspended', 'expired'])->default('pending');
            $table->decimal('payment', 8, 2)->default(0.00);
            $table->json('location')->nullable();
            $table->string('certificate_path')->nullable()->after('location'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
