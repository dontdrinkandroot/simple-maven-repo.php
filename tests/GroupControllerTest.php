<?php

namespace App\Tests;

use App\DataFixtures\Group\MavenRepositoryGroupPublic;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class GroupControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $filesystem = new Filesystem();
        $filesystem->remove('/tmp/simplemvnrepotest/');
    }

    public function testDownloadPublic()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositoryGroupPublic::class])->getReferenceRepository();

        $client = $this->makeClient();

        $client->request(Request::METHOD_GET, '/groups/public/artifact1/0.1-snapshot');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $client->getResponse();
        $this->assertEquals('snapshots', file_get_contents($binaryFileResponse->getFile()));

        $client->request(Request::METHOD_GET, '/groups/public/artifact1/0.1-release');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        /** @var BinaryFileResponse $binaryFileResponse */
        $binaryFileResponse = $client->getResponse();
        $this->assertEquals('releases', file_get_contents($binaryFileResponse->getFile()));
    }

    public function testDownloadMissing()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositoryGroupPublic::class])->getReferenceRepository();

        $client = $this->makeClient();

        $client->request(Request::METHOD_GET, '/groups/public/missingartifact/missingfile');
        $this->assertStatusCode(404, $client);
    }

    public function testList()
    {
        $referenceRepository = $this->loadFixtures([MavenRepositoryGroupPublic::class])->getReferenceRepository();

        $client = $this->makeClient();

        $client->request(Request::METHOD_GET, '/groups/public/artifact1/');
        $this->assertStatusCode(200, $client);

        $client->request(Request::METHOD_GET, '/groups/public/artifact2/');
        $this->assertStatusCode(200, $client);
    }
}
