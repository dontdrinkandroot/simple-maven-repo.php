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

class RepositoryControllerTest extends FixtureWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $filesystem = new Filesystem();
        $filesystem->remove('/tmp/simplemvnrepotest/');
    }

    public function testDownloadNotFound()
    {
        $this->loadClientAndFixtures();

        $this->client->request(Request::METHOD_GET, '/repos/nonexisting/nonexisting');

        self::assertResponseStatusCodeSame(404);
    }

    public function testDownloadPublic()
    {
        $referenceRepository = $this->loadClientAndFixtures([MavenRepositorySnapshots::class]);

        $this->client->request(Request::METHOD_GET, '/repos/snapshots/artifact1/0.1-snapshot');

        self::assertResponseStatusCodeSame(200);

        $this->assertInstanceOf(BinaryFileResponse::class, $this->client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $this->client->getResponse();
        $this->assertEquals('snapshots', file_get_contents($binaryFileResponse->getFile()));
    }

    public function testUploadNotFound()
    {
        $this->loadClientAndFixtures();

        $this->client->request(Request::METHOD_POST, '/repos/nonexisting/nonexisting', [], [], [], 'Test');

        self::assertResponseStatusCodeSame(404);
    }

    public function testUploadUnauthorized()
    {
        $referenceRepository = $this->loadClientAndFixtures([MavenRepositorySnapshots::class]);

        $this->client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            [],
            'Test'
        );

        self::assertResponseStatusCodeSame(401);
    }

    public function testUploadForbidden(): void
    {
        $this->loadClientAndFixtures([MavenRepositorySnapshots::class, UserRead::class],);

        $this->client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            ['PHP_AUTH_USER' => UserRead::USERNAME, 'PHP_AUTH_PW' => UserRead::USERNAME],
            'Test'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpload()
    {
        $referenceRepository = $this->loadClientAndFixtures(
            [MavenRepositorySnapshots::class, UserReadWrite::class],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/repos/snapshots/net/dontdrinkandroot/test.pom',
            [],
            [],
            ['PHP_AUTH_USER' => UserReadWrite::USERNAME, 'PHP_AUTH_PW' => UserReadWrite::USERNAME],
            'Test'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->request(
            Request::METHOD_GET,
            '/repos/snapshots/net/dontdrinkandroot/test.pom'
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertInstanceOf(BinaryFileResponse::class, $this->client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $this->client->getResponse();
        $this->assertEquals('Test', file_get_contents($binaryFileResponse->getFile()));
    }
}
