<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Notyfikacje miały być bytem abstrakcyjnym, niewykorzystującym ObjectManagera.
 * Niepotrzebne wykorzystanie Doctrine
 */
class MessageFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $message = new Message();
            $message->setSubject('subject '.$i);
            $message->setMessage('message '.$i);

            $manager->persist($message);
            $this->addReference('message-'.$i, $message);
        }

        $manager->flush();
    }


    /**
     * @return array
     */
    public static function getGroups():array
    {
        return ['test'];
    }

}
