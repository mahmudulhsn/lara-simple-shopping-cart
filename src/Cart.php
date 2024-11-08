<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

use Illuminate\Support\Collection;

class Cart
{
    /**
     * add product to cart
     */
    public static function add(string $id, string $name, float $price, int|float $quantity, array $extraInfo = []): object
    {
        $rowId = CartHelper::generateRowId(id: $id, productDetails: [$id, $name, $price, $quantity]);

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

        if ($extraInfo !== []) {
            $products[$rowId]['extraInfo'] = $extraInfo;
        }
        session()->put('cart.products', $products);

        $cartTotal = array_sum(array_column($products, 'sub_total'));
        session()->put('cart.total', $cartTotal);

        return self::get($rowId);
    }

    /**
     * return single product details by row id
     */
    public static function get(string $rowId): ?object
    {
        $products = session()->get('cart.products', []);

        return isset($products[$rowId]) ? (object) $products[$rowId] : null;
    }

    /**
     * update the cart item by row id
     */
    public static function update(string $rowId, array $productData): object
    {
        $products = session()->get('cart.products', []);
        if (\array_key_exists($rowId, $products)) {
            $quantity = $productData['quantity'] ?? $products[$rowId]['quantity'];
            $price = $productData['price'] ?? $products[$rowId]['price'];

            $products[$rowId]['price'] = $price;
            $products[$rowId]['quantity'] = $quantity;
            $products[$rowId]['sub_total'] = $quantity * $price;

            if (isset($productData['quantity']) && $productData['quantity'] !== []) {
                $products[$rowId]['extraInfo'] = $productData['quantity'];
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
     * remove item form cart by item id
     *
     * @throws \Exception
     */
    public static function remove(string $rowId): void
    {
        $products = session()->get('cart.products', []);
        if (\array_key_exists($rowId, $products)) {

            unset($products[$rowId]);
            session()->put('cart.products', $products);

            $cartTotal = array_sum(array_column($products, 'sub_total'));
            session()->put('cart.total', $cartTotal);
        } else {
            throw new \Exception("Product with row ID {$rowId} not found in cart.");
        }
    }

    /**
     * clear the cart
     */
    public static function destroy(): void
    {
        session()->put('cart.products', []);
        session()->put('cart.total', 0);
    }

    /**
     * return the total of the cart
     * @return int|float
     */
    public static function total(): int|float
    {
        return session()->get('cart.total', 0);
    }

    /**
     * return the content of the cart
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function content(): Collection
    {
        $products = session()->get('cart.products');
        return collect($products);
    }
}
