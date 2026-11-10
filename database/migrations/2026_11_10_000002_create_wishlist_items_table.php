<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('wishlist.table_prefix', 'itmosoft_');

        Schema::create($prefix.'wishlist_items', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained($prefix.'wishlists')->cascadeOnDelete();
            $table->string('item_type');
            $table->string('item_id');
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->unique(['wishlist_id', 'item_type', 'item_id'], 'wishlist_item_unique');
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('wishlist.table_prefix', 'itmosoft_').'wishlist_items');
    }
};
