<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TweetFixtures extends BaseFixture implements DependentFixtureInterface
{
    private static $list_text = [
        'Les médias d’information peuvent-ils s’épanouir sur Twitch ?',
        'Dans quelques jours, promis ! Dimanche, lundi max !',
        'Je crois que vous avez gagné alors ? 😉❤️',
        'J’en ai bien l’intention !',
        'Pas avant dimanche prochain, au plus tôt... Désolé !',
        'Les records sont faits pour être battus, 3H40 de revue de presse ?',
        '🔥 Nouveau concours pour fêter les 300K followers (merci la team ! ❤) Gagne une PS5 + une 2ème manette + un jeu ! 🎮',
        '1460 Elo, je sais pas ce qui m\'arrive je smurf absolument toutes les games',
    ];

    public function loadData(ObjectManager $manager)
    {


        $this->createMany(Tweet::class, 20, function (Tweet $tweet, $count) {
            $tweet->setContent(self::$list_text[$this->faker->numberBetween(0, 7)]);
            $tweet->setAuthor($this->getReference(User::class . '_' . $this->faker->numberBetween(0, 9)));
            $tweet->setCreatedAt($this->faker->dateTimeBetween('-1 months', '-1 seconds'));
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
