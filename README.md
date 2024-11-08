## Lara Simple Shopping Cart

A simple shopping cart implementation for Laravel.

## Installation

Install the package through [Composer](http://getcomposer.org/). 

Run the Composer require command from the Terminal:

    composer require mahmudulhsn/lara-simple-shopping-cart
    
Run this command for publishing the provider.
    
    php artisan vendor:publish --provider="Mahmudulhsn\LaraSimpleShoppingCart\SimpleShoppingCartServiceProvider"

Run this command for publishing the config. You can change the config according to you needs.
    
    php artisan vendor:publish --tag="lara-simple-shopping-cart-config"


## Overview
Look at one of the following topics to learn more about LarSimpleShoppingCart

* [Usage](#usage)

## Usage

The Lara Simple Shopping Cart gives you the following methods to use:

### Cart::add()

Adding an item to the cart is really simple, you just use the `add()` method, which accepts a variety of parameters.

In its most basic form you can specify the id, name, quantity, price of the product you'd like to add to the cart.

```php
Cart::add('293ad', 'Product 1', 1, 9.99);
```

As an optional fifth parameter you can pass extra info as an array.

```php
Cart::add('293ad', 'Product 1', 1, 9.99, ['color' => 'Red', 'size' => 'XL']);
```

**The `add()` method will return an CartItem instance of the item you just added to the cart.**

### Cart::update()

To update an item in the cart, you'll first need the rowId of the item.
Next you can use the `update()` method to update it.

```php
$rowId = 'd2ab10bd906390824d60c40d94093333';
Cart::update($rowId, ['quantity' => 5, 'price' => 500]);
```

You can update other info like this.

```php
$rowId = 'd2ab10bd906390824d60c40d94093333';
Cart::update($rowId, ['id'=> '293ad', 'name' => 'Product one updated', 'quantity' => 5, 'price' => 500]);
```
You can also update extra info by passing the info as an array.

```php
$rowId = 'd2ab10bd906390824d60c40d94093333';
Cart::update($rowId, ['id'=> '293ad', 'name' => 'Product one updated', 'quantity' => 5, 'price' => 500, 'extraInfo' => ['color' => 'Red', 'size' => 'XL']]);
```

### Cart::remove()

To remove an item for the cart, you'll again need the rowId. This rowId you simply pass to the `remove()` method and it will remove the item from the cart.

```php
$rowId = 'd2ab10bd906390824d60c40d94093333';

Cart::remove($rowId);
```

### Cart::get()

If you want to get an item from the cart using its rowId, you can simply call the `get()` method on the cart and pass it the rowId.

```php
$rowId = 'd2ab10bd906390824d60c40d94093333';

Cart::get($rowId);
```

### Cart::content()

Of course you also want to get the carts content. This is where you'll use the `content` method. This method will return a Collection of CartItems which you can iterate over and show the content to your customers.

```php
Cart::content();
```

### Cart::destroy()

If you want to completely remove the content of a cart, you can call the destroy method on the cart. This will remove all CartItems from the cart for the current cart instance.

```php
Cart::destroy();
```

### Cart::total()

The `total()` method can be used to get the calculated total of all items in the cart, given there price and quantity.

```php
Cart::total();
```
