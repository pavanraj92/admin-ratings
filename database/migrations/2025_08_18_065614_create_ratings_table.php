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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            // Product (optional)
            if (Schema::hasTable('products')) {
                $table->foreignId('product_id')->nullable()
                      ->constrained('products')
                      ->cascadeOnDelete();
            } else {
                $table->unsignedBigInteger('product_id')->nullable();
            }

            // Course (optional)
            if (Schema::hasTable('courses')) {
                $table->foreignId('course_id')->nullable()
                      ->constrained('courses')
                      ->cascadeOnDelete();
            } else {
                $table->unsignedBigInteger('course_id')->nullable();
            }

            $table->tinyInteger('rating')->nullable()->comment('Rating out of 5');
            $table->text('review')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Status: pending, approved, rejected');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
