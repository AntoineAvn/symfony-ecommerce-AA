<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Nom du produit',
                ],
                'label' => false,
            ])
            ->add('excerpt', CKEditorType::class, [
                'label' => 'Extrait',
            ])
            ->add('description', CKEditorType::class)
            ->add('image', null, [
                'attr' => [
                    'placeholder' => 'Image du produit',
                ],
                'label' => false,
            ])
            ->add('quantity', null, [
                'attr' => [
                    'placeholder' => 'Quantité du produit',
                ],
                'label' => false,
            ])
            ->add('price', null, [
                'attr' => [
                    'placeholder' => 'Prix du produit',
                ],
                'label' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Disponible' => 1,
                    'Bientôt en stock' => 2,
                    'Indisponible' => 3,
                ],
            ])
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
