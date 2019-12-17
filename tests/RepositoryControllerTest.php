<?php

namespace App\Tests;

use App\DataFixtures\Repositories\MavenRepositorySnapshots;
use App\DataFixtures\Users\UserRead;
use App\DataFixtures\Users\UserReadWrite;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class RepositoryControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $filesystem = new Filesystem();
        $filesystem->remove('/tmp/simplemvnrepotest/');
    }

    public function testDownloadNotFound()
    {
        $client = $this->makeClient();

        $client->request(Request::METHOD_GET, '/repos/nonexisting/nonexisting');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDownloadPublic()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositorySnapshots::class])->getReferenceRepository();

        $client = $this->makeClient();

        $client->request(Request::METHOD_GET, '/repos/snapshots/artifact1/0.1-snapshot');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $client->getResponse();
        $this->assertEquals('snapshots', file_get_contents($binaryFileResponse->getFile()));
    }

    public function testUploadNotFound()
    {
        $client = $this->makeClient();

        $client->request(Request::METHOD_POST, '/repos/nonexisting/nonexisting', [], [], [], 'Test');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testUploadUnauthorized()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositorySnapshots::class])->getReferenceRepository();

        $client = $this->makeClient();

        $client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            [],
            'Test'
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testUploadForbidden()
    {
        $referenceRepository = $this->loadFixtures(
            [MavenRepositorySnapshots::class, UserRead::class]
        )->getReferenceRepository();

        $client = $this->makeClient(['username' => UserRead::USERNAME, 'password' => UserRead::USERNAME]);

        $client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            [],
            'Test'
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUpload()
    {
        $referenceRepository = $this->loadFixtures(
            [MavenRepositorySnapshots::class, UserReadWrite::class]
        )->getReferenceRepository();

        $client = $this->makeClient(['username' => UserReadWrite::USERNAME, 'password' => UserReadWrite::USERNAME]);

        $client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            [],
            'Test'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $client->request(
            Request::METHOD_GET,
            '/repos/snapshots/net/dontdrinkandroot/test.pom'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $client->getResponse();
        $this->assertEquals('Test', file_get_contents($binaryFileResponse->getFile()));
    }
}
