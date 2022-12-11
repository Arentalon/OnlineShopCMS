<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    public function __construct(
        private EmailVerifier $emailVerifier,
        private ManagerRegistry $doctrine
    ){}

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @param ShopRepository $shopRepository
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        ShopRepository $shopRepository,
        TranslatorInterface $translator
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            /** @var Shop $shopName */
            $shopName = $shopRepository->findOneBy(['attrName' => 'name']);
            $name = null !== $shopName ? $shopName->getAttrValue() : 'Shop';
            $emailTemplate = 'en' === $request->getLocale() ? 'registration/confirmation_email_en.html.twig' : 'registration/confirmation_email_pl.html.twig';

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('cms@gmail.com', $name))
                    ->to($user->getMail())
                    ->subject($translator->trans('success', [], 'mail.confirmation'))
                    ->htmlTemplate($emailTemplate)
            );
            // do anything else you need here, like send an email
            $this->addFlash('success', $translator->trans('flash.mail'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $id = $request->get('id');

        if (null === $id)
            return $this->redirectToRoute('app_login');

        $user = $userRepository->find($id);

        if (null === $user)
            return $this->redirectToRoute('app_login');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', $translator->trans('flash.mail_confirmed'));

        return $this->redirectToRoute('app_login');

    }
}
