<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EditProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fileUpload',FileType::class, [
                'label' => 'form.product.photo',
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
            ])
            ->add('name', TextType::class, [
                'label' => 'form.product.name',
            ])
            ->add('price', NumberType::class, [
                'label' => 'form.product.price',
            ])
            ->add('priceUnit', ChoiceType::class, [
                'label' => 'form.product.price_unit',
                'choices' => Product::PRICE_UNIT,
            ])
            ->add('amount', NumberType::class, [
                'label' => 'form.product.amount',
            ])
            ->add('categoryId', EntityType::class, [
                'label' => 'form.product.category',
                'class' => Category::class,
                'placeholder' => 'form.product.other',
                'choice_label' => function ($category) {return $category->getName();},
                'required' => false,
            ])
            ->add('startDate', DateType::class, [
                'label' => 'form.product.start_date',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'form.product.end_date',
                'widget' => 'single_text',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'dd/mm/yyyy',
                ],
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'form.product.is_active',
                'attr' => [
                    'checked' => 'checked',
                ],
                'required' => false,
            ])

            ->add('description', TextareaType::class, [
                'label' => 'form.product.description',
                'required' => false,
            ])
            ->add('producer', TextType::class, [
                'label' => 'form.product.producent',
                'required' => false,
            ])
            ->add('lang', ChoiceType::class, [
                'label' => 'form.product.lang',
                'choices' => User::LANGUAGES,
                'placeholder' => 'all_lang',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
