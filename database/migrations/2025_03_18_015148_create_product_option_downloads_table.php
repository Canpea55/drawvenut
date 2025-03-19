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
        Schema::create('product_option_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_options_id')->constrained()->onDelete('cascade');
            $table->string('link', 2048);
            $table->string('type', 256);
            $table->boolean('isDownload')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_downloads');
    }
};
