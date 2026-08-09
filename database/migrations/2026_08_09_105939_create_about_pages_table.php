<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('subtitle')->nullable();

            $table->longText('content');
            $table->longText('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->longText('values')->nullable();

            $table->string('image')->nullable();

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};