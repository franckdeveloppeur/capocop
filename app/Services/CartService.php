<?php

namespace App\Services;

use App\Models\Product;
use Darryldecode\Cart\Cart as ShoppingCart;

class CartService
{
    /**
     * Add a product to cart
     */
    public static function addProduct(string $productId, int $quantity = 1, array $attributes = []): bool
    {
        try {
            $product = Product::findOrFail($productId);

            // Prepare cart item data
            $cartItem = [
                'id' => $product->id,
                'name' => $product->title,
                'price' => $product->price_promo ?? $product->base_price,
                'quantity' => $quantity,
                'attributes' => array_merge([
                    'base_price' => $product->base_price,
                    'slug' => $product->slug,
                    'image' => self::getProductImage($product),
                ], $attributes),
            ];

            \Cart::add($cartItem);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Remove a product from cart
     */
    public static function removeProduct(string $productId): bool
    {
        try {
            \Cart::remove($productId);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Update product quantity in cart
     */
    public static function updateQuantity(string $productId, int $quantity): bool
    {
        try {
            \Cart::update($productId, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity,
                ],
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get cart total quantity
     */
    public static function getTotalQuantity(): int
    {
        try {
            return \Cart::getTotalQuantity();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Get cart total price
     */
    public static function getTotalPrice(): float
    {
        try {
            return (float) \Cart::getTotal();
        } catch (\Throwable $e) {
            return 0.0;
        }
    }

    /**
     * Get all cart items
     */
    public static function getItems()
    {
        try {
            return \Cart::getContent();
        } catch (\Throwable $e) {
            return collect([]);
        }
    }

    /**
     * Clear cart
     */
    public static function clear(): bool
    {
        try {
            \Cart::clear();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get product image URL
     */
    private static function getProductImage(Product $product): ?string
    {
        try {
            $media = $product->media->first();
            if ($media) {
                $path = data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'));
                return asset('storage/' . $path);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }
}
