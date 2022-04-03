<?php

namespace App\Service;

//use

use App\Entity\Cart;
use App\Repository\CartItemsRepository;
use App\Repository\ProductRepository;

class CartItemsService
{
    private CartItemsRepository $cartItemsRepository;
    private ProductRepository $productRepository;

    function __construct(CartItemsRepository $cartItemsRepository, ProductRepository $productRepository)
    {
        $this->cartItemsRepository = $cartItemsRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Cart | null $cart
     * @return array
     */
    public function collectItems(?Cart $cart): array
    {
        $allProductsInCartPrice = 0;
        $productsInCart = [];
        $cartItems = $cart ? $this->cartItemsRepository->findBy(['cartId' => $cart->getCartId()]) : [];
        foreach ($cartItems as $value) {
            $product = $this->productRepository->findOneBy(['productId' => $value->getProductId()]);
            if (!empty($product)) {
                $allPrice = $product->getPrice() * $value->getAmount();
                $allProductsInCartPrice += $allPrice;
                $productInCart = array(
                    'id' => $value->getProductId()->getProductId(),
                    'cartId' => $value->getCartId()->getCartId(),
                    'amount' => $value->getAmount(),
                    'img' => $product->getImg(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'priceUnit' => $product->getPriceUnit(),
                    'allPrice' => $allPrice,
                );
                $productsInCart[] = $productInCart;
            }
        }

        return [$productsInCart, $allProductsInCartPrice];
    }
}