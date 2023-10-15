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
        // table insta_user
        //      id
        //      user_id
        //      insta_username
        //      insta_id
        //      access_token
        //      access_token_expires_in
        //      created_at
        //      updated_at

        Schema::create('insta_users', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->string('insta_username');
            $table->string('insta_id');
            $table->string('access_token');
            $table->string('access_token_expires_in');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insta_users');
    }
};
