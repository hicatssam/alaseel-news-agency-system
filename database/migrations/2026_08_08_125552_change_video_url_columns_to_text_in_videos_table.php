<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->text('thumbnail')->nullable()->change();
            $table->text('video_url')->nullable()->change();
            $table->text('embed_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('thumbnail', 255)->nullable()->change();
            $table->string('video_url', 255)->nullable()->change();
            $table->string('embed_url', 255)->nullable()->change();
        });
    }
};