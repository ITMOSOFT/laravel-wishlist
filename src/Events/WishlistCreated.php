<?php

namespace ItmoSoft\Wishlist\Events;

use ItmoSoft\Wishlist\Models\Wishlist;

class WishlistCreated
{
    public function __construct(public Wishlist $wishlist) {}
}
