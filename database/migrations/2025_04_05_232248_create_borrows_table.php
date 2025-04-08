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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->nullable();
            $table->enum('type' , ['personal' , 'impersonal']);
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('new_product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('from')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('to')->nullable()->constrained('sections')->onDelete('set null');
            $table->timestamp('borrowed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('borrow');
    }
};
