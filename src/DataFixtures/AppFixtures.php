<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for ($i = 0; $i < 10; $i++) { 
            $microPost = new MicroPost();
            $microPost->setText('Aliquot qui excepteur adipisicing cupidatat veniam exercitation ad cupidatat.'.rand(0, 1000));
            $microPost->setTime(new \DateTime('2020-11-28'));
            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
