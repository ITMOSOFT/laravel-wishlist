<?php

namespace ItmoSoft\Wishlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ItmoSoft\Wishlist\Concerns\HasWishlistTable;

class Wishlist extends Model
{
    use HasWishlistTable;

    protected string $wishlistTable = 'wishlists';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class, 'wishlist_id');
    }

    public function contains(string $itemType, int|string $itemId): bool
    {
        return $this->items()->where('item_type', $itemType)
            ->where('item_id', (string) $itemId)->exists();
    }
}
