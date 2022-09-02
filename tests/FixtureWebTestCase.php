<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Dontdrinkandroot\Common\Asserted;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FixtureWebTestCase extends WebTestCase
{
    protected ReferenceRepository $referenceRepository;

    protected KernelBrowser $client;

    protected function loadClientAndFixtures(
        array $classNames = [],
        bool $catchExceptions = true
    ): ReferenceRepository {
        $this->client = static::createClient();
        $databaseTool = Asserted::instanceOf(
            self::$container->get(DatabaseToolCollection::class),
            DatabaseToolCollection::class
        )->get();
        $this->referenceRepository = $databaseTool->loadFixtures($classNames)->getReferenceRepository();
        $this->client->catchExceptions($catchExceptions);

        return $this->referenceRepository;
    }
}
