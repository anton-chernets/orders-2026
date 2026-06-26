<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('seed_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('seeder_class');
            $table->timestamp('applied_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seed_versions');
    }
};
