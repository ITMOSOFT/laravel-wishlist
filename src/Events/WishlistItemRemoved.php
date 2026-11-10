<?php

namespace ItmoSoft\Wishlist\Events;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\Wishlist\Models\Wishlist;

class WishlistItemRemoved
{
    public function __construct(public Wishlist $wishlist, public Model $item) {}
}
