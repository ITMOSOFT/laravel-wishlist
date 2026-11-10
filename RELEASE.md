# Release Guide

## Repository
- Repository: `ITMOSOFT/laravel-wishlist`
- Description: `Wishlist management for Laravel applications, supporting multiple wishlists, product items, guest users and authenticated customers.`

## v1.0.0
**Title:** `v1.0.0 — Initial Release`

### Release notes
- Add customer and guest wishlists.
- Add default wishlist creation and retrieval.
- Add polymorphic wishlist items with duplicate prevention.
- Add, remove and move wishlist items.
- Merge guest wishlist items into a customer's default wishlist.
- Add wishlist lifecycle events and configurable table prefixes.
- Add PHPUnit unit and feature tests.
- Add Laravel 12 and Laravel 13 GitHub Actions matrix with explicit PHPUnit execution.

## Before publishing
1. Install dependencies: `composer install`
2. Validate Composer: `composer validate --strict`
3. Run tests: `composer test`
4. Push the branch and verify both Laravel 12 and Laravel 13 GitHub Actions jobs pass.
5. Create and push the tag:
   ```bash
   git tag -a v1.0.0 -m "Initial release"
   git push origin v1.0.0
   ```
6. Create the GitHub Release using the title and notes above.
7. Submit `itmosoft/laravel-wishlist` to Packagist and verify the package updates.

Do not publish until both CI matrix jobs pass.
