<?php

namespace App\Tests;

use App\DataFixtures\Repositories\MavenRepositorySnapshots;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class RepositoryControllerTest extends WebTestCase
{
    public function testGetNotFound()
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/repos/nonexisting/nonexisting');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testPostNotFound()
    {
        $client = static::createClient();

        $client->request(Request::METHOD_POST, '/repos/nonexisting/nonexisting');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUpload()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositorySnapshots::class])->getReferenceRepository();

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request(Request::METHOD_POST, '/repos/snapshots/net/dontdrinkandroot/test.pom');

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
}
