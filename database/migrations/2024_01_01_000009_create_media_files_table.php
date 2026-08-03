<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['image','video','audio','document']);
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('folder')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('file_type');
            $table->index('folder');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
