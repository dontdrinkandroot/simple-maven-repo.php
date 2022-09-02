<?php

namespace App\Controller;

use App\Security\SecurityService;
use App\Service\MavenRepositoryGroupService;
use App\Service\MavenRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class IndexController extends AbstractController
{
    private MavenRepositoryService $mavenRepositoryService;

    private MavenRepositoryGroupService $mavenRepositoryGroupService;

    private SecurityService $securityService;

    public function __construct(
        SecurityService $securityService,
        MavenRepositoryService $mavenRepositoryService,
        MavenRepositoryGroupService $mavenRepositoryGroupService,
    ) {
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->mavenRepositoryGroupService = $mavenRepositoryGroupService;
        $this->securityService = $securityService;
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
