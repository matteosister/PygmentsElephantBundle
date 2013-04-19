<?php

namespace Cypress\PygmentsElephantBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/_pygments_bundle/style.css');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
