<?php

namespace ItmoSoft\Wishlist\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ItmoSoft\Wishlist\Events\WishlistCreated;
use ItmoSoft\Wishlist\Events\WishlistItemAdded;
use ItmoSoft\Wishlist\Events\WishlistItemRemoved;
use ItmoSoft\Wishlist\Events\WishlistMerged;
use ItmoSoft\Wishlist\Models\Wishlist;
use ItmoSoft\Wishlist\Models\WishlistItem;

class WishlistManager
{
    public function create(
        int|string|null $customerId = null,
        ?string $name = null,
        ?string $guestToken = null,
        bool $default = false
    ): Wishlist {
        if ($customerId === null && ($guestToken === null || $guestToken === '')) {
            throw new InvalidArgumentException('A customer ID or guest token is required.');
        }

        if ($customerId === null && ! config('wishlist.guests.enabled', true)) {
            throw new InvalidArgumentException('Guest wishlists are disabled.');
        }

        $wishlist = Wishlist::create([
            'customer_id' => $customerId,
            'guest_token' => $guestToken,
            'name' => $name ?: config('wishlist.default_name', 'My Wishlist'),
            'is_default' => $default,
        ]);

        event(new WishlistCreated($wishlist));

        return $wishlist;
    }

    public function findOrCreateDefault(int|string|null $customerId = null, ?string $guestToken = null): Wishlist
    {
        if ($customerId === null && ($guestToken === null || $guestToken === '')) {
            throw new InvalidArgumentException('A guest token is required for a guest wishlist.');
        }

        $query = Wishlist::query()->where('is_default', true);

        if ($customerId !== null) {
            $query->where('customer_id', $customerId)->whereNull('guest_token');
        } else {
            if (! config('wishlist.guests.enabled', true)) {
                throw new InvalidArgumentException('Guest wishlists are disabled.');
            }
            $query->whereNull('customer_id')->where('guest_token', $guestToken);
        }

        return $query->first() ?? $this->create($customerId, null, $guestToken, true);
    }

    public function add(Wishlist $wishlist, Model $item, array $attributes = []): WishlistItem
    {
        $itemType = $item->getMorphClass();
        $itemId = (string) $item->getKey();

        $existing = $wishlist->items()
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->first();

        if ($existing) {
            return $existing;
        }

        $wishlistItem = $wishlist->items()->create(array_merge($attributes, [
            'item_type' => $itemType,
            'item_id' => $itemId,
        ]));

        event(new WishlistItemAdded($wishlist, $wishlistItem));

        return $wishlistItem;
    }

    public function remove(Wishlist $wishlist, Model $item): bool
    {
        $deleted = (bool) $wishlist->items()
            ->where('item_type', $item->getMorphClass())
            ->where('item_id', (string) $item->getKey())
            ->delete();

        if ($deleted) {
            event(new WishlistItemRemoved($wishlist, $item));
        }

        return $deleted;
    }

    public function move(WishlistItem $item, Wishlist $destination): WishlistItem
    {
        if ((string) $item->wishlist_id === (string) $destination->getKey()) {
            return $item;
        }

        $duplicate = $destination->items()
            ->where('item_type', $item->item_type)
            ->where('item_id', $item->item_id)
            ->first();

        if ($duplicate) {
            $item->delete();
            return $duplicate;
        }

        $item->wishlist()->associate($destination);
        $item->save();

        return $item;
    }

    public function mergeGuestWishlist(string $guestToken, int|string $customerId): Wishlist
    {
        return DB::transaction(function () use ($guestToken, $customerId) {
            $guestWishlists = Wishlist::query()->where('guest_token', $guestToken)->get();
            $customerWishlist = $this->findOrCreateDefault($customerId);

            foreach ($guestWishlists as $guestWishlist) {
                foreach ($guestWishlist->items as $item) {
                    $exists = $customerWishlist->items()
                        ->where('item_type', $item->item_type)
                        ->where('item_id', $item->item_id)
                        ->exists();

                    if (! $exists) {
                        $item->wishlist()->associate($customerWishlist);
                        $item->save();
                    } else {
                        $item->delete();
                    }
                }

                if ($guestWishlist->id !== $customerWishlist->id) {
                    $guestWishlist->delete();
                }
            }

            event(new WishlistMerged($guestToken, $customerWishlist));

            return $customerWishlist;
        });
    }

    public function guestToken(): string
    {
        return (string) Str::uuid();
    }
}
