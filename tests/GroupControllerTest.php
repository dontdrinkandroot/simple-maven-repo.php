<?php

namespace App\Tests;

use App\DataFixtures\Group\MavenRepositoryGroupPublic;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class GroupControllerTest extends FixtureWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $filesystem = new Filesystem();
        $filesystem->remove('/tmp/simplemvnrepotest/');
    }

    public function testDownloadPublic()
    {
        $referenceRepository = $this->loadClientAndFixtures([MavenRepositoryGroupPublic::class]);

        $this->client->request(Request::METHOD_GET, '/groups/public/artifact1/0.1-snapshot');

        self::assertResponseStatusCodeSame(200);

        $this->assertInstanceOf(BinaryFileResponse::class, $this->client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $this->client->getResponse();
        $this->assertEquals('snapshots', file_get_contents($binaryFileResponse->getFile()));

        $this->client->request(Request::METHOD_GET, '/groups/public/artifact1/0.1-release');

        self::assertResponseStatusCodeSame(200);

        $this->assertInstanceOf(BinaryFileResponse::class, $this->client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $this->client->getResponse();
        $this->assertEquals('releases', file_get_contents($binaryFileResponse->getFile()));
    }

    public function testDownloadMissing()
    {
        $referenceRepository = $this->loadClientAndFixtures([MavenRepositoryGroupPublic::class]);

        $this->client->request(Request::METHOD_GET, '/groups/public/missingartifact/missingfile');
        self::assertResponseStatusCodeSame(404);
    }

    public function testList()
    {
        $referenceRepository = $this->loadClientAndFixtures([MavenRepositoryGroupPublic::class]);

        $this->client->request(Request::METHOD_GET, '/groups/public/artifact1/');
        self::assertResponseStatusCodeSame(200);

        $this->client->request(Request::METHOD_GET, '/groups/public/artifact2/');
        self::assertResponseStatusCodeSame(200);
    }
}
