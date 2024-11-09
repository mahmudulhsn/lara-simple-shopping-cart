<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

use Illuminate\Support\Collection;
use Mahmudulhsn\LaraSimpleShoppingCart\CartHelper;

class Cart
{
    /**
     * @var string Root session key for storing cart data.
     */
    protected string $rootSessionKey;

    /**
     * @var CartHelper Helper class for cart operations.
     */
    protected CartHelper $cartHelper;

    /**
     * Cart constructor.
     *
     * @param string $rootSessionKey Root session key.
     * @param CartHelper $cartHelper Instance of CartHelper.
     */
    public function __construct(string $rootSessionKey, CartHelper $cartHelper)
    {
        $this->rootSessionKey = $rootSessionKey;
        $this->cartHelper = $cartHelper;
    }

    /**
     * Add product to cart.
     *
     * @param string $id Product ID.
     * @param string $name Product name.
     * @param float $price Product price.
     * @param int|float $quantity Product quantity.
     * @param array $extraInfo Additional product info.
     * @return object Added product as an object.
     */
    public function add(string $id, string $name, float $price, int|float $quantity, array $extraInfo = []): object
    {
        $rowId = $this->cartHelper->generateRowId($id, [$id, $name, $price, $quantity]);
        $quantity = max(1, $quantity); // Ensure quantity is at least 1
        $products = session()->get("{$this->rootSessionKey}.products", []);

        $products[$rowId] = [
            'rowId' => $rowId,
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $quantity * $price,
        ];

        if (!empty($extraInfo)) {
            $products[$rowId]['extraInfo'] = $extraInfo;
        }

        session()->put("{$this->rootSessionKey}.products", $products);
        $this->cartHelper->updateTotal($this->rootSessionKey);

        return $this->get($rowId);
    }

    /**
     * Get single product details by row ID.
     *
     * @param string $rowId Row ID of the product.
     * @return object|null Product as an object or null if not found.
     */
    public function get(string $rowId): ?object
    {
        $products = session()->get("{$this->rootSessionKey}.products", []);
        return isset($products[$rowId]) ? (object) $products[$rowId] : null;
    }

    /**
     * Update cart item by row ID.
     *
     * @param string $rowId Row ID of the product.
     * @param array $productData Data to update (quantity, price, extraInfo).
     * @return object Updated product as an object.
     * @throws \Exception If product is not found in cart.
     */
    public function update(string $rowId, array $productData): object
    {
        $products = session()->get("{$this->rootSessionKey}.products", []);
        if (array_key_exists($rowId, $products)) {
            $quantity = $productData['quantity'] ?? $products[$rowId]['quantity'];
            $price = $productData['price'] ?? $products[$rowId]['price'];

            $products[$rowId]['price'] = $price;
            $products[$rowId]['quantity'] = $quantity;
            $products[$rowId]['subtotal'] = $quantity * $price;

            if (isset($productData['extraInfo']) && !empty($productData['extraInfo'])) {
                $products[$rowId]['extraInfo'] = $productData['extraInfo'];
            }

            session()->put("{$this->rootSessionKey}.products", $products);
            $this->cartHelper->updateTotal($this->rootSessionKey);

            return $this->get($rowId);
        }

        throw new \Exception("Product with row ID {$rowId} not found in cart.");
    }

    /**
     * Remove item from cart by row ID.
     *
     * @param string $rowId Row ID of the product to remove.
     * @throws \Exception If product is not found in cart.
     */
    public function remove(string $rowId): void
    {
        $products = session()->get("{$this->rootSessionKey}.products", []);
        if (array_key_exists($rowId, $products)) {
            unset($products[$rowId]);
            session()->put("{$this->rootSessionKey}.products", $products);
            $this->cartHelper->updateTotal($this->rootSessionKey);
        } else {
            throw new \Exception("Product with row ID {$rowId} not found in cart.");
        }
    }

    /**
     * Clear the cart.
     */
    public function destroy(): void
    {
        session()->put("{$this->rootSessionKey}.products", []);
        session()->put("{$this->rootSessionKey}.total", 0);
        session()->put("{$this->rootSessionKey}.subtotal", 0);
        session()->put("{$this->rootSessionKey}.discount", 0);
    }

    /**
     * Get total of the cart.
     *
     * @return int|float Total amount of the cart.
     */
    public function total(): int|float
    {
        return session()->get("{$this->rootSessionKey}.total", 0);
    }

    /**
     * Get subtotal of the cart.
     *
     * @return int|float Subtotal amount of the cart.
     */
    public function subtotal(): int|float
    {
        return session()->get("{$this->rootSessionKey}.subtotal", 0);
    }

    /**
     * Get discount applied to the cart.
     *
     * @return int|float Discount amount applied to the cart.
     */
    public function discountTotal(): int|float
    {
        return session()->get("{$this->rootSessionKey}.discount", 0);
    }

    /**
     * Get all products in the cart.
     *
     * @return \Illuminate\Support\Collection Collection of products.
     */
    public function content(): Collection
    {
        $products = session()->get("{$this->rootSessionKey}.products", []);
        return collect($products);
    }

    /**
     * Apply discount to the entire cart.
     *
     * @param int|float $amount Discount amount to apply.
     * @param string|null $discountType Type of discount (e.g., 'fix' or 'percentage').
     */
    public function applyDiscount(int|float $amount, ?string $discountType = 'fix'): void
    {
        $this->cartHelper->updateDiscount($this->rootSessionKey, $amount, $discountType);
    }
}
