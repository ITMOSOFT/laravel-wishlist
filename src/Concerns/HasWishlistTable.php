<?php

namespace ItmoSoft\Wishlist\Concerns;

trait HasWishlistTable
{
    public function getTable(): string
    {
        return config('wishlist.table_prefix', 'itmosoft_').$this->wishlistTable;
    }
}
