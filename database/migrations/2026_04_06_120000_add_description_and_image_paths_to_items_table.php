<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'description')) {
                $table->text('description')->nullable()->after('location');
            }

            if (!Schema::hasColumn('items', 'image_paths')) {
                $table->json('image_paths')->nullable()->after('image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'image_paths')) {
                $table->dropColumn('image_paths');
            }

            if (Schema::hasColumn('items', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
