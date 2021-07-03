<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $user=new User();
        $user->setFirstName('super');
        $user->setLastName("admin");
        $user->setemail('superadmin@gmail.com');
        $user->setRoles(['ROLE_SyperAdmin']);

        $user->setPassword($this->passwordEncoder->encodePassword( $user,'1234'));

        $manager->persist($user);
        $manager->flush();
    }
}
