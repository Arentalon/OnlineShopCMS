<?php

namespace App\Form;

use App\Entity\Shop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopDataRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (ChoiceType::class === $options['formTypeClass']) {
            $builder
                ->add('attrValue', $options['formTypeClass'], [
                    'label' => false,
                    'choices' => $options['choices'],
                    'placeholder' => $options['placeholder'],
                ])
            ;
        } else {
            $builder
                ->add('attrValue', $options['formTypeClass'], [
                    'label' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
            'formTypeClass' => TextType::class,
            'choices' => null,
            'placeholder' => null,
        ]);
    }
}
