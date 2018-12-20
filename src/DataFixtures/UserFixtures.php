<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{

    // todo: config it
    private const STANDARD_USER_ATTRIBUTE = 1;
    private const PORTAL_USER_ATTRIBUTE = 2;
    private const STATUS_ACTIVE = 1;
    private const STATUS_INACTIVE = 0;

    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user
                ->setAttribute(self::STANDARD_USER_ATTRIBUTE)
                ->setName('User '.$i)
                ->setStatus(self::STATUS_ACTIVE);
            $manager->persist($user);
            $this->addReference('user-'.$i, $user);
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
