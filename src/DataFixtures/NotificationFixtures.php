<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {

        // standard notifications
        for ($i = 1; $i < 10; $i++) {
            $notification = new Notification();
            $notification
                ->setTitle('notification '.$i)
                ->setDescription('description '.$i)
                ->setSender($manager->merge($this->getReference('user-'.($i+1)))) // make different than recipient
                ->setRecipient($manager->merge($this->getReference('user-'.$i)))
                ->setContext($manager->merge($this->getReference('message-'.$i)))
                ->setType($manager->merge($this->getReference('notification-type-user')));
            $manager->persist($notification);
        }

        // system notifications
        for ($i = 10; $i < 19; $i++) {
            $notification = new Notification();
            $notification
                ->setTitle('system notification '.$i)
                ->setDescription('description '.$i)
                ->setSender($manager->merge($this->getReference('user-portal')))
                ->setRecipient($manager->merge($this->getReference('user-'.$i)))
                ->setContext($manager->merge($this->getReference('message-'.$i)))
                ->setType($manager->merge($this->getReference('notification-type-system')))
                ->setMeta(['priority' => mt_rand(0, 255)]);
            $manager->persist($notification);
        }

        $manager->flush();
    }


    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            PortalUserFixtures::class,
            UserFixtures::class,
            MessageFixtures::class,
            NotificationTypeFixtures::class,
        ];
    }

    /**
     * @return array
     */
    public static function getGroups(): array
    {
        return ['test'];
    }

}
