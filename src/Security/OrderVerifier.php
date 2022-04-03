<?php

namespace App\Security;

use App\Entity\Cart;
use App\Entity\History;
use App\Entity\HistoryProduct;
use App\Entity\User;
use App\Repository\CartItemsRepository;
use App\Repository\CartRepository;
use App\Repository\HistoryProductRepository;
use App\Repository\HistoryRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class OrderVerifier
{
    private $verifyEmailHelper;
    private $mailer;
    private $entityManager;
    private CartRepository $cartRepository;
    private CartItemsRepository $cartItemsRepository;
    private HistoryRepository $historyRepository;
    private HistoryProductRepository $historyProductRepository;
    private ShopRepository $shopRepository;

    public function __construct(
        VerifyEmailHelperInterface $helper,
        MailerInterface $mailer,
        EntityManagerInterface $manager,
        CartRepository $cartRepository,
        CartItemsRepository $cartItemsRepository,
        HistoryRepository $historyRepository,
        HistoryProductRepository $historyProductRepository,
        ShopRepository $shopRepository
    )
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->entityManager = $manager;
        $this->cartRepository = $cartRepository;
        $this->cartItemsRepository = $cartItemsRepository;
        $this->historyRepository = $historyRepository;
        $this->historyProductRepository = $historyProductRepository;
        $this->shopRepository = $shopRepository;
    }

    public function sendOrderConfirmation(string $verifyOrderRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyOrderRouteName,
            $user->getUserId(),
            $user->getMail(),
            ['id' => $user->getUserId()],
        );

        $context = $email->getContext();
        $accountNumber = $this->shopRepository->findOneBy(['attrName' => 'accountNumber']);
        $context['accountNumber'] = null !== $accountNumber ? $accountNumber->getAttrValue() : null;
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @param Request $request
     * @param UserInterface & User $user
     * @throws VerifyEmailExceptionInterface
     */
    public function handleOrderConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getUserId(), $user->getMail());
        $cart = $this->cartRepository->findOneBy([
            'userId' => $user->getUserId(),
            'status' => Cart::STATUS_CLOSED,
        ]);
        $cartItems = $this->cartItemsRepository->findBy(['cartId' => $cart->getCartId()]);
        $history = new History();
        $history->setDate(new \DateTime())->setUserId($user);
        $this->entityManager->persist($history);
        $this->entityManager->flush();
        foreach ($cartItems as $cartItem) {
            $historyProduct = new HistoryProduct();
            $historyProduct
                ->setHistoryId($history)
                ->setName($cartItem->getProductId()->getName())
                ->setPrice($cartItem->getProductId()->getPrice())
                ->setAmount($cartItem->getAmount())
            ;
            $this->entityManager->persist($historyProduct);
            $this->entityManager->remove($cartItem);
        }
        $this->entityManager->remove($cart);

        $this->entityManager->flush();
    }
}
