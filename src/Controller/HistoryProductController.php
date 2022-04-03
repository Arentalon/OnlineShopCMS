<?php

namespace App\Controller;

use App\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;
use App\Repository\HistoryProductRepository;
use App\Repository\HistoryRepository;
use App\Repository\CartRepository;
use App\Repository\CartItemsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryProductController extends AbstractController
{
    /**
     * @Route("/history/product", name="history_product")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param HistoryProductRepository $historyProductRepository
     * @param HistoryRepository $historyRepository
     * @param CartRepository $cartRepository
     * @param CartItemsRepository $cartItemsRepository
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository, HistoryProductRepository $historyProductRepository, HistoryRepository $historyRepository, CartRepository $cartRepository, CartItemsRepository $cartItemsRepository): Response
    {
        $user = $this->getUser();
        $historyProductList = [];
        $waitingProductList = [];
        $history = $historyRepository->findBy(['userId' => $user]);
        $cart = $cartRepository->findOneBy(['userId' => $user, 'status' => Cart::STATUS_CLOSED]);
        if (!empty($history)) {
            $count = 0;
            foreach ($history as $value) {
                $count ++;
                $fullPrice = 0;
                $historyProduct = $historyProductRepository->findBy(['historyId' => $value->getHistoryId()]);
                foreach ($historyProduct as $price) {
                    $fullPrice += $price->getPrice();
                }
                $historyProductList[] = array(
                    'purchase' => $count,
                    'fullPrice' => $fullPrice,
                    'date' => $value->getDate()->format('Y-m-d'),
                    'historyProduct' => $historyProduct
                );
            }
        }
        if (!empty($cart)) {
            $cartItems = $cartItemsRepository->findBy(['cartId' => $cart->getCartId()]);
            foreach ($cartItems as $value) {
                $product = $productRepository->findOneBy(['productId' => $value->getProductId()]);
                $waitingProductList[] = array(
                    'name' => $product->getName(),
                    'amount' => $value->getAmount(),
                    'price' => $product->getPrice()
                );
            }
        }
        
        return $this->render('history_product/index.html.twig', [
            'historyProductList' => $historyProductList,
            'waitingProductList' => $waitingProductList
        ]);
    }
}
