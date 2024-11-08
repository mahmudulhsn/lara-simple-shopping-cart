<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

class CartHelper
{
    /**
     * Generate a unique id for the cart item.
     */
    public static function generateRowId(string $id, array $productDetails): string
    {
        ksort($productDetails);
        return md5($id . serialize($productDetails));
    }
}
