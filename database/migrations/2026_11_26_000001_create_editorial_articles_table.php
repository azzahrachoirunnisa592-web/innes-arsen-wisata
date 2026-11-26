<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_articles', function (Blueprint $table) {
            $table->id();
            $table->string('section', 40)->index();
            $table->string('slug', 180)->unique();
            $table->string('title', 180);
            $table->string('summary', 500);
            $table->text('image');
            $table->string('alt', 255);
            $table->json('body');
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_articles');
    }
};
