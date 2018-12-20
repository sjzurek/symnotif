<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PortalUserFixtures extends Fixture implements FixtureGroupInterface
{

    // todo: config it
    private const STANDARD_USER_ATTRIBUTE = 1;
    private const PORTAL_USER_ATTRIBUTE = 2;
    private const STATUS_ACTIVE = 1;
    private const STATUS_INACTIVE = 0;

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setAttribute(self::PORTAL_USER_ATTRIBUTE)
            ->setName('Portal')
            ->setStatus(self::STATUS_ACTIVE);
        $manager->persist($user);
        $this->addReference('user-portal', $user);

        $manager->flush();
    }


    /**
     * @return array
     */
    public static function getGroups():array
    {
        return ['install'];
    }

}
