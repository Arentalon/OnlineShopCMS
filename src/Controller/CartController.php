<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartItemsRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Service\CartItemsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param CartItemsRepository $cartItemsRepository
     * @param ProductRepository $productRepository
     * @param CartItemsService $cartItemsService
     * @return Response
     */
    public function index(
        Request $request,
        CartRepository $cartRepository,
        CartItemsRepository $cartItemsRepository,
        ProductRepository $productRepository,
        CartItemsService $cartItemsService
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $removeCartItemProductId = $request->request->get('removeCartItemProductId');
        if ($removeCartItemProductId) {
            $cart = $cartRepository->findOneBy(['userId' => $this->getUser()->getUserId()]);
            $removeCartItems = $cartItemsRepository->findOneBy(['productId' => $removeCartItemProductId, 'cartId' => $cart->getCartId()]);
            $product = $productRepository->findOneBy(['productId' => $removeCartItemProductId]);
            $product->setAmount($product->getAmount() + $removeCartItems->getAmount());
            $entityManager=$this->getDoctrine()->getManager();  
            $entityManager->persist($product);
            $entityManager->remove($removeCartItems);
            $entityManager->flush();
            print json_encode('Udało się');
                    die();
        }

        $cart = $cartRepository->findOneBy([
            'userId' => $this->getUser()->getUserId(),
            'status' => Cart::STATUS_OPEN,
            ]);
        [$productsInCart, $allProductsInCartPrice] = $cartItemsService->collectItems($cart);

        $cartInProgress = $cartRepository->findOneBy([
            'userId' => $this->getUser()->getUserId(),
            'status' => Cart::STATUS_CLOSED,
        ]);
        [$productsInProgress, $allProductsInProgressPrice] = $cartItemsService->collectItems($cartInProgress);

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'productsInCart' => $productsInCart,
            'allProductsInCartPrice' => $allProductsInCartPrice,
            'productsInProgress' => $productsInProgress,
            'allProductsInProgressPrice' => $allProductsInProgressPrice,
        ]);
    }
}
