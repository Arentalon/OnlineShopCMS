<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Podaj nowe hasło',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Twoje hasło musi mieć co najmniej {{ limit }} znaków',
                            'maxMessage' => 'Twoje hasło nie może przekraczać {{ limit }} znaków',
                            // max length allowed by Symfony for security reasons
                            'max' => 30,
                        ]),
                    ],
                    'label' => 'form.change_password.new',
                ],
                'second_options' => [
                    'label' => 'form.change_password.confirm',
                ],
                'invalid_message' => 'change_password.invalid_message',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
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
