<?php

namespace App\Controller;

use App\Entity\MavenRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class MavenRepositoryController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $filesystem;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->filesystem = new Filesystem();
    }

    public function download(MavenRepository $mavenRepository, string $path)
    {
        $this->logger->info(sprintf('Download, repo: %s, path: %s', $mavenRepository->getShortName(), $path));

//        if ($this->endsWith($path, 'maven-metadata.xml')) {
//            throw new NotFoundHttpException();
//        }
//
//        if ($this->endsWith($path, 'maven-metadata.xml.sha1')) {
//            return new Response(sha1("ok"));
//        }
//
//        return new Response("ok");

        $fileSystemPath = '/tmp/simplemavenrepo/' . $mavenRepository->getShortName() . '/' . $path;
        if (!file_exists($fileSystemPath)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($fileSystemPath);
    }

    public function upload(Request $request, MavenRepository $mavenRepository, string $path)
    {
        $this->logger->info(
            sprintf('Upload, repo: %s, path: %s', $mavenRepository->getShortName(), $path)
        );

        $fileSystemFile = '/tmp/simplemavenrepo/' . $mavenRepository->getShortName() . '/' . $path;
        $fileSystemDirectory = dirname($fileSystemFile);

        if (!$this->filesystem->exists($fileSystemDirectory)) {
            $this->filesystem->mkdir($fileSystemDirectory);
        }

        $resource = $request->getContent(true);
        file_put_contents($fileSystemFile, $resource);

//        if ($this->endsWith($path, '.xml')) {
//            $this->logger->info($request->getContent());
//        }
//
//        if ($this->endsWith($path, '.pom')) {
//            $this->logger->info($request->getContent());
//        }

        return new Response(null, Response::HTTP_CREATED);
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
