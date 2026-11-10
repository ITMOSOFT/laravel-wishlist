<?php

namespace ItmoSoft\Wishlist\Events;

use ItmoSoft\Wishlist\Models\Wishlist;

class WishlistMerged
{
    public function __construct(public string $guestToken, public Wishlist $wishlist) {}
}
