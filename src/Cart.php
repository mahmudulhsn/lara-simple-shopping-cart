<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

use Illuminate\Support\Collection;

class Cart
{
    /**
     * Add product to cart.
     *
     * @param string $id
     * @param string $name
     * @param float $price
     * @param int|float $quantity
     * @param array $extraInfo
     * @return object
     */
    public static function add(string $id, string $name, float $price, $quantity, array $extraInfo = []): object
    {
        // Replace named arguments with positional arguments
        $rowId = CartHelper::generateRowId($id, [$id, $name, $price, $quantity]);

        if (!session()->has('cart')) {
            session()->put('cart', [
                'products' => [],
                'total' => 0,
            ]);
        }

        $quantity = $quantity < 1 ? 1 : $quantity;

        $products = session()->get('cart.products', []);

        $products[$rowId] = [
            'rowId' => $rowId,
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'sub_total' => $quantity * $price,
        ];

        if (!empty($extraInfo)) {
            $products[$rowId]['extraInfo'] = $extraInfo;
        }
        session()->put('cart.products', $products);

        $cartTotal = array_sum(array_column($products, 'sub_total'));
        session()->put('cart.total', $cartTotal);

        return self::get($rowId);
    }

    /**
     * Return single product details by row ID.
     *
     * @param string $rowId
     * @return object|null
     */
    public static function get(string $rowId): ?object
    {
        $products = session()->get('cart.products', []);

        return isset($products[$rowId]) ? (object) $products[$rowId] : null;
    }

    /**
     * Update the cart item by row ID.
     *
     * @param string $rowId
     * @param array $productData
     * @return object
     * @throws \Exception
     */
    public static function update(string $rowId, array $productData): object
    {
        $products = session()->get('cart.products', []);
        if (array_key_exists($rowId, $products)) {
            $quantity = $productData['quantity'] ?? $products[$rowId]['quantity'];
            $price = $productData['price'] ?? $products[$rowId]['price'];

            $products[$rowId]['price'] = $price;
            $products[$rowId]['quantity'] = $quantity;
            $products[$rowId]['sub_total'] = $quantity * $price;

            if (isset($productData['extraInfo']) && !empty($productData['extraInfo'])) {
                $products[$rowId]['extraInfo'] = $productData['extraInfo'];
            }

            session()->put('cart.products', $products);
            $cartTotal = array_sum(array_column($products, 'sub_total'));
            session()->put('cart.total', $cartTotal);

            return self::get($rowId);
        } else {
            throw new \Exception("Product with row ID {$rowId} not found in cart.");
        }
    }

    /**
     * Remove item from cart by item ID.
     *
     * @param string $rowId
     * @throws \Exception
     */
    public static function remove(string $rowId): void
    {
        $products = session()->get('cart.products', []);
        if (array_key_exists($rowId, $products)) {
            unset($products[$rowId]);
            session()->put('cart.products', $products);

            $cartTotal = array_sum(array_column($products, 'sub_total'));
            session()->put('cart.total', $cartTotal);
        } else {
            throw new \Exception("Product with row ID {$rowId} not found in cart.");
        }
    }

    /**
     * Clear the cart.
     */
    public static function destroy(): void
    {
        session()->put('cart.products', []);
        session()->put('cart.total', 0);
    }

    /**
     * Return the total of the cart.
     *
     * @return int|float
     */
    public static function total()
    {
        return session()->get('cart.total', 0);
    }

    /**
     * Return the content of the cart.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function content(): Collection
    {
        $products = session()->get('cart.products', []);
        return collect($products);
    }
}
