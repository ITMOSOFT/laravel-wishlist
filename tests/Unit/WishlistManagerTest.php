<?php

namespace ItmoSoft\Wishlist\Tests\Unit;

use ItmoSoft\Wishlist\Services\WishlistManager;
use ItmoSoft\Wishlist\Tests\TestCase;

class WishlistManagerTest extends TestCase
{
    public function test_guest_token_is_a_valid_uuid(): void
    {
        $token = app(WishlistManager::class)->guestToken();

        $this->assertTrue((bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $token
        ));
    }
}
