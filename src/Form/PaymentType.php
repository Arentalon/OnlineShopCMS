<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', TextType::class, [
                'label' => 'form.account.city',
            ])
            ->add('country', TextType::class, [
                'label' => 'form.account.country',
            ])
            ->add('street', TextType::class, [
                'label' => 'form.account.street',
            ])
            ->add('nr', TextType::class, [
                'label' => 'form.account.nr',
            ])
            ->add('code', TextType::class, [
                'label' => 'form.account.code',
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
