<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('wishlist.table_prefix', 'itmosoft_');

        Schema::create($prefix.'wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->uuid('guest_token')->nullable()->index();
            $table->string('name', 120);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['customer_id', 'is_default']);
            $table->index(['guest_token', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('wishlist.table_prefix', 'itmosoft_').'wishlists');
    }
};
