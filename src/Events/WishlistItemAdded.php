<?php

namespace ItmoSoft\Wishlist\Events;

use ItmoSoft\Wishlist\Models\Wishlist;
use ItmoSoft\Wishlist\Models\WishlistItem;

class WishlistItemAdded
{
    public function __construct(public Wishlist $wishlist, public WishlistItem $item) {}
}
