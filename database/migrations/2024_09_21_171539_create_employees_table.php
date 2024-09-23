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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('jwt_token')->nullable();
            $table->string('redmine_token')->nullable();
            $table->timestamp('token_expire')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->bigInteger('redmine_id')->nullable();
            $table->string('profile_photo')->nullable();
            $table->boolean('notifications')->default(1);
            $table->boolean('alerts')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
