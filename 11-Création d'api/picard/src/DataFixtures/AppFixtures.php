<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->word())
                    ->setImage($faker->imageUrl())
                    ->setDescription($faker->text())
                    ->setPrice($faker->randomFloat(2, 1, 100))
                    ->setRating($faker->randomFloat(1, 0, 5))
                    ->setAvailable($faker->boolean());

            $manager->persist($product);
        }


        for ($i = 0; $i < 5; $i++) {
            $cart = new Cart();
            $cart->setStatus('pending');

            foreach ($manager->getRepository(Product::class)->findAll() as $product) {
                $cart->addProduct($product);
            }

            $manager->persist($cart);
        }

        $manager->flush();
    }
}
