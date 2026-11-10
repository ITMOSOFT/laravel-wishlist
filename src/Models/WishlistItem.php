<?php

namespace ItmoSoft\Wishlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ItmoSoft\Wishlist\Concerns\HasWishlistTable;

class WishlistItem extends Model
{
    use HasWishlistTable;

    protected string $wishlistTable = 'wishlist_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array'];
    }

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class, 'wishlist_id');
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }
}
