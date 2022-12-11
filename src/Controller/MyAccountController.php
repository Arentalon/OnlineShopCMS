<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyAccountType;
use App\Form\ChangePasswordType;
use Doctrine\Persistence\ManagerRegistry;
use App\EventSubscriber\UserLocaleSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MyAccountController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    /**
     * @Route("/my/account", name="my_account")
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        TranslatorInterface $translator
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(MyAccountType::class, $user);
        $form->handleRequest($request);
        $passForm = $this->createForm(ChangePasswordType::class, $user);
        $passForm->handleRequest($request);

        $entityManager = $this->doctrine->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            if ($request->getLocale() !== $user->getLang()) {
                $request->getSession()->set('_locale', $user->getLang());
                $request->setLocale($user->getLang());
            }

            $this->addFlash('success', $translator->trans('flash.data_change'));
        }

        if ($passForm->isSubmitted() && $passForm->isValid()) {
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $passForm->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('flash.pass_change'));
        }

        return $this->render('my_account/index.html.twig', [
            'controller_name' => 'MyAccountController',
            'myAccountForm' => $form->createView(),
            'passForm' => $passForm->createView(),
        ]);
    }
}
