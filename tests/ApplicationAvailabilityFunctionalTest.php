<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    public function testGlobalAvailability()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('success', $client->getResponse()->getContent());
    }

    public function testUserNotifications()
    {
        $client = static::createClient();
        $client->request('GET', '/users/1/notifications');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('notifications', $client->getResponse()->getContent());
    }
}
