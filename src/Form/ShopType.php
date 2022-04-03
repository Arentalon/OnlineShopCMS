<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ShopDataRowType::class, [
                'label' => 'form.shop.name',
            ])
            ->add('logo',FileType::class, [
                'label' => 'Logo',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'maxSizeMessage' => 'product.max_size',
                        'mimeTypesMessage' => 'product.mime'
                    ])
                ],
            ])
            ->add('accountNumber', ShopDataRowType::class, [
                'label' => 'form.shop.account',
            ])
            ->add('adminLangView', ShopDataRowType::class, [
                'label' => 'form.shop.lang_view',
                'formTypeClass' => ChoiceType::class,
                'choices' => User::LANGUAGES,
                'placeholder' => 'all_lang',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
