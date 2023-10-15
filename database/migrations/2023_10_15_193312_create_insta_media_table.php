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
        // table insta_media
        //      id
        //      insta_user_id
        //      insta_media_username
        //      insta_media_id
        //      insta_media_type
        //      insta_media_url
        //      insta_media_caption
        //      insta_media_permalink
        //      insta_media_timestamp
        //      created_at
        //      updated_at

        Schema::create('insta_media', function (Blueprint $table) {
            $table->id();
            $table->string('insta_user_id');
            $table->string('insta_media_username');
            $table->string('insta_media_id');
            $table->string('insta_media_type');
            $table->string('insta_media_url');
            $table->string('insta_media_caption');
            $table->string('insta_media_permalink');
            $table->string('insta_media_timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insta_media');
    }
};
