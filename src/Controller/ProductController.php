<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\CartItems;
use App\Form\EditProductType;
use App\Form\AddCartItemsType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\CartItemsRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine) {}

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * @Route("/show_product/{productId}/{isEditMode}/{isCreateMode}", name="show_product")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ProductRepository $productRepository
     * @param CartRepository $cartRepository
     * @param CartItemsRepository $cartItemsRepository
     * @param int|null $productId
     * @param string|null $isEditMode
     * @param string|null $isCreateMode
     * @param string $plus
     * @param float $value
     * @return Response
     */
    public function showProduct(Request $request, TranslatorInterface $translator, ProductRepository $productRepository, CartRepository $cartRepository, CartItemsRepository $cartItemsRepository, int $productId = null, string $isEditMode = null, string $isCreateMode = null, string $plus = 'false', float $value = 0.5): Response
    {
        $this->translator = $translator;
        /** @var User $user */
        $user = $this->getUser();
        $isAdmin = (in_array("ROLE_ADMIN", $user ? $user->getRoles() : []));

        if ($isAdmin) {
            $product = null !== $productId ? $productRepository->find($productId) : new Product();
            return $this->editProduct($request, $product);
        } else {
            $addProductIdToCart = $request->request->get('productId');
            $addProductAmountToCart = $request->request->get('productAmount');
            if ($addProductIdToCart && $addProductAmountToCart) {
                $isUser = (in_array("ROLE_USER", $user ? $user->getRoles() : []));
                if ($isUser) {
                    $cart = $cartRepository->findOneBy([
                        'userId' => $user->getUserId(),
                        'status' => Cart::STATUS_OPEN,
                    ]);
                    if (!$cart) {
                        $cart = new Cart();
                        $cart->setUserId($user);
                        $cart->setStatus(Cart::STATUS_OPEN);
                        $this->upsert($cart);
                    }

                    $cartItems = $cartItemsRepository->findOneBy(['cartId' => $cart->getCartId(), 'productId' => $addProductIdToCart]);
                    if ($cartItems) {
                        $product = $productRepository->findOneBy(['productId' => $addProductIdToCart]);
                        $product->setAmount($product->getAmount() - $addProductAmountToCart);
                        $this->upsert($product);
                        $addProductAmountToCart += $cartItems->getAmount();
                        $cartItems->setAmount($addProductAmountToCart);
                        $this->upsert($cartItems);
                        print json_encode('addedToCart');
                        die();
                    } else {
                        $product = $productRepository->findOneBy(['productId' => $addProductIdToCart]);
                        $cartItemObject = new CartItems();
                        $cartItemObject->setProductId($product);
                        $cartItemObject->setAmount($addProductAmountToCart);
                        $cartItemObject->setCartId($cart);
                        $this->upsert($cartItemObject);
                        $product->setAmount($product->getAmount() - $addProductAmountToCart);
                        $this->upsert($product);
                        print json_encode('addedToCart');
                        die(); 
                    }
                } else {
                    print json_encode('login');
                    die();
                }
                
            }
        }

        $products = ($productId ? $productRepository->findBy(array('productId' => $productId)) : []);   

        return $this->render('product/product.html.twig',  ['products' => $products,'isEditMode' => $isEditMode, 'isCreateMode' => $isCreateMode, 'value' => $value]);
    }

    public function upsert($obj) {
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($obj);
        $entityManager->flush(); 
    }

    public function editProduct(Request $request, Product $product)
    {
        $productForm = $this->createForm(EditProductType::class, $product);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $em = $this->doctrine->getManager();
            /** @var UploadedFile $imageFileName */
            $imageFileName = $productForm->get('fileUpload')->getData();
            if ($imageFileName) {
                try {
                    $originalFileName = pathinfo($imageFileName->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = transliterator_transliterate('Any-latin; Latin-ASCII; [^A-Za-z0-9] remove; Lower()', $originalFileName);
                    $newFileName = $safeFileName.'-'.uniqid().'.'.$imageFileName->guessExtension();
                    $imageFileName->move('productsImages', $newFileName);

                    $product->setImg($newFileName);
                } catch (\Exception $e) {
                    $this->addFlash('error', $this->translator->trans('flash.error'));
                }
            }

            $em->persist($product);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('flash.product').' '.$product->getName().' '.$this->translator->trans('flash.product_update'));
        }

        return $this->render('product/editProduct.html.twig',  ['productForm' => $productForm->createView()]);
    }

}
