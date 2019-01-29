<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class RepositoryControllerTest extends WebTestCase
{
    public function testUpload()
    {
        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request(Request::METHOD_POST, '/repos/snapshots/net/dontdrinkandroot/test.pom');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
