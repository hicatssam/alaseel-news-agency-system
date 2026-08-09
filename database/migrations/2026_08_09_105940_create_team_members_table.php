<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('job_title');
            $table->string('image')->nullable();

            $table->unsignedInteger('display_order')
                ->default(0)
                ->index();

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'is_active',
                'display_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};