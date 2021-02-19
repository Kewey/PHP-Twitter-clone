<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // public const USER_REFERENCE = 'Jojo';
    private static $names = [
        'Jojo',
        'Tom',
        'Michel',
        'Dealabs',
        'Paypal',
        'Simon',
        'Claire',
        'Sardoche',
        'Samuel',
        'Ponce',
        'Antoine Daniel',
    ];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 10, function (User $user, $count) use ($manager) {
            $user->setUsername(self::$names[$count]);
            $password = $this->encoder->encodePassword($user, '123456');
            $user->setPassword($password);
        });

        $manager->flush();
    }
}
