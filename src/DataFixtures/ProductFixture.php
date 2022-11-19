<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixture extends Fixture {

    public function load(ObjectManager $manager): void {
        $count = 2000;
        $categoryCount = 10;

        /** @var Category[] */
        $categories = array_map(function ($i) use ($manager) {
            $category = new Category();
            $category->setName("Category $i");
            $category->setIsActive(true);
            $manager->persist($category);

            return $category;
        }, range(0, $categoryCount - 1));

        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setIsActive(true);
            $product->setPriceUnit('zł/szt');
            $product->setProducer("Producer $i");
            $product->setPrice(round($this->getRandFloat(0, 100), 2));
            $product->setAmount(rand(1, 10));
            $product->setDescription("Description of product $i");
            $product->setAmountOfBought(rand(0, 1000));
            $product->setStartDate(new DateTime());

            $categoryIndex = rand(0, $categoryCount - 1);
            $product->setCategoryId($categories[$categoryIndex]);
            $manager->persist($product);
        }

        $manager->flush();
    }

    private function getRandFloat(float $min = 0, float $max = 1) {
        return $min + ($max - $min) * (float)rand() / (float)getrandmax();
    }
}
