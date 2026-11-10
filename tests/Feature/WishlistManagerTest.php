<?php

namespace ItmoSoft\Wishlist\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\Wishlist\Models\Wishlist;
use ItmoSoft\Wishlist\Services\WishlistManager;
use ItmoSoft\Wishlist\Tests\TestCase;

class WishlistProduct extends Model
{
    protected $table = 'test_products';
    protected $guarded = [];
    public $timestamps = false;
}

class WishlistManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->run();

        $this->app['db']->connection()->getSchemaBuilder()->create('test_products', function ($table) {
            $table->id();
            $table->string('name');
        });
    }

    public function test_it_creates_a_guest_default_wishlist(): void
    {
        $wishlist = app(WishlistManager::class)->findOrCreateDefault(null, 'guest-token');

        $this->assertSame('guest-token', $wishlist->guest_token);
        $this->assertTrue($wishlist->is_default);
        $this->assertSame(1, Wishlist::count());
    }

    public function test_it_adds_an_item_only_once(): void
    {
        $manager = app(WishlistManager::class);
        $wishlist = $manager->create(10);
        $product = WishlistProduct::create(['name' => 'Book']);

        $first = $manager->add($wishlist, $product);
        $second = $manager->add($wishlist, $product);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, $wishlist->items()->count());
    }

    public function test_moving_an_item_to_its_current_wishlist_does_not_delete_it(): void
    {
        $manager = app(WishlistManager::class);
        $wishlist = $manager->create(10);
        $product = WishlistProduct::create(['name' => 'Book']);
        $item = $manager->add($wishlist, $product);

        $moved = $manager->move($item, $wishlist);

        $this->assertSame($item->id, $moved->id);
        $this->assertSame(1, $wishlist->items()->count());
    }

    public function test_it_merges_guest_items_into_customer_default_wishlist(): void
    {
        $manager = app(WishlistManager::class);
        $guestWishlist = $manager->create(null, 'Guest list', 'guest-token', true);
        $customerWishlist = $manager->create(25, 'Saved items', null, true);
        $product = WishlistProduct::create(['name' => 'Book']);
        $manager->add($guestWishlist, $product);

        $merged = $manager->mergeGuestWishlist('guest-token', 25);

        $this->assertSame($customerWishlist->id, $merged->id);
        $this->assertSame(1, $merged->items()->count());
        $this->assertDatabaseMissing('itmosoft_wishlists', ['id' => $guestWishlist->id]);
    }
}
