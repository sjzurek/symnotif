<?php

namespace App\DataFixtures;

use App\Entity\NotificationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class NotificationTypeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $systemType = new NotificationType();
        $systemType->setLabel('System');
        $systemType->setSlug('system');

        $userType = new NotificationType();
        $userType->setLabel('User Message');
        $userType->setSlug('user');

        $manager->persist($systemType);
        $manager->persist($userType);

        $this->addReference('notification-type-system', $systemType);
        $this->addReference('notification-type-user', $userType);

        $manager->flush();
    }

    /**
     * @return array
     */
    public static function getGroups(): array
    {
        return ['install'];
    }

}
