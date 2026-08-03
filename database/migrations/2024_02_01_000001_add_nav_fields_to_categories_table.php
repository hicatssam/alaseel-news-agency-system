<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_in_header')->default(true)->after('sort_order');
            $table->boolean('show_in_footer')->default(true)->after('show_in_header');
            $table->boolean('show_on_homepage')->default(true)->after('show_in_footer');
            $table->string('color')->nullable()->after('show_on_homepage');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_in_header','show_in_footer','show_on_homepage','color']);
        });
    }
};
