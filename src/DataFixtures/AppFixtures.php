<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $names = [
            'Jojo',
            'Tom',
            'Michel'
        ];

        foreach ($names as $name) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, '123456');
            $user->setPassword($password);
            $user->setUsername($name);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
