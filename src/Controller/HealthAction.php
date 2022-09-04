<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Dontdrinkandroot\Common\Asserted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthAction
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
    }

    public function __invoke(Request $request): Response
    {
        $data = ['base_uri' => $request->getSchemeAndHttpHost(), 'database' => []];
        $connections = $this->managerRegistry->getConnections();
        foreach ($connections as $name => $connection) {
            $connection = Asserted::instanceOf($connection, Connection::class);
            $connection->executeQuery('SELECT 1')->fetchAllAssociative();
            $platform = Asserted::notNull($connection->getDatabasePlatform());
            $data['database'][$name] = get_class($platform);
        }

        return new JsonResponse($data);
    }
}
