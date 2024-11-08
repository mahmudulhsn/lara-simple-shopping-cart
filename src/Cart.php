<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

class Cart
{
    /**
     * add product to cart
     */
    public static function add(array $productData, array $extraInfo = []): void
    {
        $rowId = self::generateRowId(id: $productData['id'], productDetails: $productData);

        if (!session()->has('cart')) {
            session()->put('cart', [
                'products' => [],
                'total' => 0,
            ]);
        }

        $productData['quantity'] = $productData['quantity'] < 1 ? 1 : $productData['quantity'];

        $products = session()->get('cart.products', []);

        $products[$rowId] = [
            'id' => $productData['id'],
            'name' => $productData['name'],
            'price' => $productData['price'],
            'quantity' => $productData['quantity'],
            'sub_total' => $productData['quantity'] * $productData['price'],
            'extraInfo' => $extraInfo,
        ];
        session()->put('cart.products', $products);

        $cartTotal = array_sum(array_column($products, 'sub_total'));
        session()->put('cart.total', $cartTotal);
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
    public static function update(string $rowId, array $productData): void
    {
        $products = session()->get('cart.products', []);
        if (\array_key_exists($rowId, $products)) {
            $quantity = $productData['quantity'] ?? $products[$rowId]['quantity'];
            $price = $productData['price'] ?? $products[$rowId]['price'];

            $products[$rowId]['price'] = $price;
            $products[$rowId]['quantity'] = $quantity;
            $products[$rowId]['sub_total'] = $quantity * $price;

            session()->put('cart.products', $products);
            $cartTotal = array_sum(array_column($products, 'sub_total'));
            session()->put('cart.total', $cartTotal);
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
    public static function clear(): void
    {
        session()->put('cart.products', []);
        session()->put('cart.total', 0);
    }

    /**
     * Generate a unique id for the cart item.
     */
    public static function generateRowId(string $id, array $productDetails): string
    {
        ksort($productDetails);
        return md5($id . serialize($productDetails));
    }
}
