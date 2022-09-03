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
        private readonly MavenRepositoryGroupService $mavenRepositoryGroupService
    ) {
    }

    public function index(): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'mavenRepositories' => $this->mavenRepositoryService->listReadableRepositories(
                    $this->securityService->findCurrentUser()
                ),
                'mavenRepositoryGroups' => $this->mavenRepositoryGroupService->listReadableGroups(
                    $this->securityService->findCurrentUser()
                )
            ]
        );
    }
}
