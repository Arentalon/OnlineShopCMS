<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'form.registration.agree',
                'label_html' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'registration.agreement',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'registration.pass_empty',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'registration.pass_min',
                            'maxMessage' => 'registration.pass_max',
                            // max length allowed by Symfony for security reasons
                            'max' => 30,
                        ]),
                    ],
                    'label' => 'form.registration.pass',
                ],
                'second_options' => [
                    'label' => 'form.registration.pass_confirm',
                ],
                'invalid_message' => 'registration.pass_unique',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
            ->add('mail', TextType::class, [
                'label' => 'E-mail',
            ])
            ->add('name', TextType::class, [
                'label' => 'form.account.name',
                'label_html' => true,
            ])
            ->add('surname', TextType::class, [
                'label' => 'form.account.surname',
                'label_html' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'form.account.city',
                'required' => false,
            ])
            ->add('country', TextType::class, [
                'label' => 'form.account.country',
                'required' => false,
            ])
            ->add('street', TextType::class, [
                'label' => 'form.account.street',
                'required' => false,
            ])
            ->add('nr', TextType::class, [
                'label' => 'form.account.nr',
                'required' => false,
            ])
            ->add('code', TextType::class, [
                'label' => 'form.account.code',
                'required' => false,
            ])
            ->add('lang', ChoiceType::class, [
                'label' => 'form.account.lang',
                'choices' => User::LANGUAGES,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
