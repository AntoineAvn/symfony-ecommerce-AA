<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('excerpt')
            ->add('description')
            ->add('image')
            ->add('quantity')
            // ->add('sold')
            ->add('price')
            ->add('status')
            /* ->add('createdAt')
            ->add('updateAt') */
            // ->add('users')
            // ->add('seller')
            ->add('category', EntityType::class, [
                "class" => Category::class,
                "label" => "Categorie",
                "choice_label" => "name"
            ])
            ->add('brand', EntityType::class, [
                "class" => Brand::class,
                "label" => "Brand",
                "choice_label" => "name"
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
