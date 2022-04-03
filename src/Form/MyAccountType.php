<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MyAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mail', TextType::class, [
                'label' => 'form.account.mail',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'form.account.name',
                'required' => false,
            ])
            ->add('surname', TextType::class, [
                'label' => 'form.account.surname',
                'required' => false,
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
