<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

class Cart
{
    /**
     * add product to cart
     * @param array $productData
     * @param array $option
     * @return void
     */
    public static function add(array $productData, array $extraInfo = []): void
    {
        $rowId = self::generateRowId(id: $productData['id'], productDetails: $productData);

        // Initialize cart if it doesn't exist
        if (!session()->has('cart')) {
            session()->put('cart', [
                'products' => [],
                'total' => 0,
            ]);
        }

        // Ensure product quantity is at least 1
        $productData['quantity'] = $productData['quantity'] < 1 ? 1 : $productData['quantity'];

        // Retrieve current products in the cart
        $products = session()->get('cart.products', []);

        // Add or update product in the cart
        $products[$rowId] = [
            'id' => $productData['id'],
            'name' => $productData['name'],
            'price' => $productData['price'],
            'quantity' => $productData['quantity'],
            'sub_total' => $productData['quantity'] * $productData['price'],
            'extraInfo' => $extraInfo,
        ];

        // Save the updated products in session
        session()->put('cart.products', $products);

        // Recalculate the total
        $cartTotal = array_sum(array_column($products, 'sub_total'));

        // Update the cart total in session
        session()->put('cart.total', $cartTotal);

    }

    /**
     * return single product details by row id
     * @param string $rowId
     * @return object|null
     */
    public static function get(string $rowId): object|null
    {
        $products = session()->get('cart.products', []);

        return isset($products[$rowId]) ? (object) $products[$rowId] : null;
    }

    /**
     * update the cart item by row id
     * @param string $rowId
     * @param array $productData
     * @return string
     */
    public static function update(string $rowId, array $productData): void
    {
        // Retrieve current products in the cart
        $products = session()->get('cart.products', []);

        // Check if the product exists in the cart
        if (\array_key_exists($rowId, $products)) {
            // Update the quantity and price from the provided data or use current values
            $quantity = $productData['quantity'] ?? $products[$rowId]['quantity'];
            $price = $productData['price'] ?? $products[$rowId]['price'];

            // Update product details
            $products[$rowId]['price'] = $price;
            $products[$rowId]['quantity'] = $quantity;
            $products[$rowId]['sub_total'] = $quantity * $price;

            // Save the updated products in session
            session()->put('cart.products', $products);

            // Recalculate the total
            $cartTotal = array_sum(array_column($products, 'sub_total'));

            // Update the cart total in session
            session()->put('cart.total', $cartTotal);
        } else {
            throw new \Exception("Product with row ID {$rowId} not found in cart.");
        }
    }

    /**
     * Generate a unique id for the cart item.
     *
     * @param string $id
     * @param array  $productDetails
     * @return string
     */
    public static function generateRowId($id, array $productDetails): string
    {
        ksort($productDetails);

        return md5($id . serialize($productDetails));
    }

}
