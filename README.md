# ITMOSOFT Laravel Wishlist

Wishlist management for Laravel applications, supporting multiple wishlists, product items, guest users and authenticated customers.

## Requirements

- PHP 8.2+
- Laravel 11, 12 or 13

## Installation

```bash
composer require itmosoft/laravel-wishlist
```

Laravel auto-discovers the service provider. Publish configuration if needed:

```bash
php artisan vendor:publish --tag=wishlist-config
```

Migrations are loaded automatically. To publish them instead:

```bash
php artisan vendor:publish --tag=wishlist-migrations
php artisan migrate
```

## Configuration

The default configuration uses the `itmosoft_` table prefix. You can change it with `WISHLIST_TABLE_PREFIX`.

```php
return [
    'table_prefix' => env('WISHLIST_TABLE_PREFIX', 'itmosoft_'),
    'guests' => [
        'enabled' => true,
        'session_key' => 'itmosoft_wishlist_token',
    ],
    'default_name' => 'My Wishlist',
];
```

## Usage

```php
use ItmoSoft\Wishlist\Services\WishlistManager;

$manager = app(WishlistManager::class);
```

Create a customer wishlist:

```php
$wishlist = $manager->create(
    customerId: auth()->id(),
    name: 'Birthday ideas',
);
```

Create or retrieve a guest default wishlist. Persist this token in the user's session or a secure cookie:

```php
$guestToken = session('itmosoft_wishlist_token');

if (! $guestToken) {
    $guestToken = $manager->guestToken();
    session(['itmosoft_wishlist_token' => $guestToken]);
}

$wishlist = $manager->findOrCreateDefault(guestToken: $guestToken);
```

Add any Eloquent model as a polymorphic item:

```php
$wishlistItem = $manager->add($wishlist, $product);
```

Store optional item metadata:

```php
$manager->add($wishlist, $product, [
    'attributes' => ['variant' => 'blue'],
]);
```

Remove or move an item:

```php
$manager->remove($wishlist, $product);
$manager->move($wishlistItem, $anotherWishlist);
```

Merge a guest wishlist after login:

```php
$manager->mergeGuestWishlist($guestToken, auth()->id());
session()->forget('itmosoft_wishlist_token');
```

## Models and events

- `ItmoSoft\Wishlist\Models\Wishlist`
- `ItmoSoft\Wishlist\Models\WishlistItem`
- `WishlistCreated`
- `WishlistItemAdded`
- `WishlistItemRemoved`
- `WishlistMerged`

Wishlist items use polymorphic type/id columns. Product models do not need a package-specific trait or hard-coded product dependency.

## Integration and security notes

- `customer_id` is an unsigned bigint without a foreign key because customer tables and model names differ between applications. Adapt the migration if you require a database-level foreign key.
- Guest token lifecycle is the host application's responsibility. Persist tokens securely and do not trust arbitrary client-supplied tokens.
- Authorization is the host application's responsibility. Verify ownership before allowing a user to read or modify a wishlist.
- The package does not include HTTP routes, controllers, UI, product catalog, cart integration, or automatic login listeners.
- A unique database constraint prevents duplicate item rows within one wishlist. Catch unique-constraint violations if concurrent add requests are possible.

## Testing

```bash
composer install
composer test
```

Run suites separately:

```bash
composer test:unit
composer test:feature
```

GitHub Actions explicitly runs PHPUnit via `composer test` against Laravel 12 and Laravel 13.

## License

MIT. See [LICENSE](LICENSE).
