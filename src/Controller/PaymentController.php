<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\PaymentType;
use App\Security\OrderVerifier;
use App\Service\CartItemsService;
use App\Repository\CartRepository;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class PaymentController extends AbstractController
{
    public function __construct(
        private OrderVerifier $orderVerifier, 
        private ManagerRegistry $doctrine
    ) {}

    /**
     * @Route("/payment", name="payment")
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param ShopRepository $shopRepository
     * @param CartItemsService $cartItemsService
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(
        Request $request,
        CartRepository $cartRepository,
        ShopRepository $shopRepository,
        CartItemsService $cartItemsService,
        TranslatorInterface $translator
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } elseif (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->redirectToRoute('index');
        }

        /** @var Cart $cart */
        $cart = $cartRepository->findOneBy([
            'userId' => $user->getUserId(),
            'status' => 'Open',
        ]);
        [$productsInCart, $allProductsInCartPrice] = $cartItemsService->collectItems($cart);
        if ([] === $productsInCart) {
            return $this->redirectToRoute('cart');
        }

        $form = $this->createForm(PaymentType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $cart->setStatus(Cart::STATUS_CLOSED);
            $em->persist($cart);
            $em->persist($user);
            $em->flush();

            /** @var Shop $shopName */
            $shopName = $shopRepository->findOneBy(['attrName' => 'name']);
            $name = null !== $shopName ? $shopName->getAttrValue() : 'Shop';
            $emailTemplate = 'en' === $request->getLocale() ? 'payment/confirmation_order_en.html.twig' : 'payment/confirmation_order_pl.html.twig';
            $this->orderVerifier->sendOrderConfirmation('verify_order', $user,
                (new TemplatedEmail())
                    ->from(new Address('cms@gmail.com', $name))
                    ->to($user->getMail())
                    ->subject($translator->trans('mail.order'))
                    ->htmlTemplate($emailTemplate)
            );
            $this->addFlash('success', $translator->trans('flash.order_added_1') . $user->getMail() . $translator->trans('flash.order_added_2'));

            return $this->redirectToRoute('history_product');
        }

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
            'form' => $form->createView(),
            'productsInCart' => $productsInCart,
            'allProductsInCartPrice' => $allProductsInCartPrice,
        ]);
    }

    /**
     * @Route("/verify/order", name="verify_order")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function verifyOrder(Request $request, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $id = $request->get('id');

        if (null === $id)
            return $this->redirectToRoute('app_login');

        $user = $userRepository->find($id);

        if (null === $user)
            return $this->redirectToRoute('app_login');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->orderVerifier->handleOrderConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Zakupy zostały zrealizowane pomyślnie!');

        return $this->redirectToRoute('history_product');

    }
}
