<?php

namespace App\Controller;

use App\Security\SecurityService;
use App\Service\MavenRepositoryGroupService;
use App\Service\MavenRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly SecurityService $securityService,
        private readonly MavenRepositoryService $mavenRepositoryService,
        private readonly MavenRepositoryGroupService $mavenRepositoryGroupService,
        private readonly string $applicationName
    ) {
    }

    public function index(): Response
    {
        $user = $this->securityService->findCurrentUser();
        return $this->render(
            'index.html.twig',
            [
                'applicationName'       => $this->applicationName,
                'mavenRepositories'     => $this->mavenRepositoryService->listReadableRepositories($user),
                'mavenRepositoryGroups' => $this->mavenRepositoryGroupService->listReadableGroups($user)
            ]
        );
    }
}
