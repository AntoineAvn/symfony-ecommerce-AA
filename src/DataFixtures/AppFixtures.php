<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $hasher){

    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user->setFirstname('Antoine2');
        $user->setLastname('Avn2');
        $user->setEmail('bb@gmail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'azerty'));
        $user->setRoles(["ROLE_USER"]);
        $ur = $this->registry->getRepository(User::class);
        $ur->save($user, true); //Save non reconnu par l'IDE mais fonctionne

        $category = new Category();
        $category->setName('Voiture');
        $cr = $this->registry->getRepository(Category::class);
        $cr->save($category, true);

        $brand = new Brand();
        $brand->setName('Mercedes');
        $br = $this->registry->getRepository(Brand::class);
        $br->save($brand, true);

         // create products! Bam!
         for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setExcerpt('Lorem ipsum dolor sit rem hic pariatur fugiat, quisquam harum eos?');
            $product->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloremque sapiente animi soluta reprehenderit nam laborum obcaecati pariatur reiciendis rem unde, aut dolor quasi ipsa blanditiis quos adipisci. Et, aspernatur! Quae.');
            $product->setImage('https://images.caradisiac.com/images/6/2/8/6/186286/S0-elles-changent-tout-sauf-leur-nom-mercedes-classe-a-le-monospace-qui-devient-berline-compacte-652700.jpg');
            $product->setQuantity(mt_rand(1, 15));
            $product->setSold(mt_rand(1,5));
            $product->setPrice(mt_rand(10, 100));
            $product->setStatus(1);
            $product->setSeller($user);
            $product->setBrand($brand);
            $product->setCategory($category);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
