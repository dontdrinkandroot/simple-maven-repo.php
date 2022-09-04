<?php

namespace App\Tests;

class HealthTest extends FixtureWebTestCase
{
    public function testHealthAvailable(): void
    {
        $this->loadClientAndFixtures();

        $this->client->request('GET', '/health');
        self::assertResponseStatusCodeSame(200);
    }
}
