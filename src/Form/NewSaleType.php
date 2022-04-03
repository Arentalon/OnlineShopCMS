<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class NewSaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productId', EntityType::class, [
                'label' => 'form.sale.product',
                'class' => Product::class,
                'choice_label' => function ($product) {return $product->getName();},
            ])
            ->add('startDate', DateType::class, [
                'label' => 'form.sale.start_date',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'form.sale.end_date',
                'widget' => 'single_text',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'mm/dd/yyyy',
                ],
                'required' => false,
            ])
            ->add('info', TextType::class, [
                'label' => 'form.sale.info',
                'required' => false,
            ])
            ->add('backImg',FileType::class, [
                'label' => 'form.sale.back_img',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'maxSizeMessage' => 'product.max_size',
                        'mimeTypesMessage' => 'product.mime',
                    ])
                ],
                'required' => false,
            ])
            ->add('lang', ChoiceType::class, [
                'label' => 'form.sale.lang',
                'choices' => User::LANGUAGES,
                'placeholder' => 'all_lang',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
