<?php

namespace App\Controller;

use App\Security\SecurityService;
use App\Service\MavenRepositoryGroupService;
use App\Service\MavenRepositoryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class IndexController
{
    private MavenRepositoryService $mavenRepositoryService;

    private EngineInterface $templatingEngine;

    private MavenRepositoryGroupService $mavenRepositoryGroupService;

    private SecurityService $securityService;

    public function __construct(
        SecurityService $securityService,
        MavenRepositoryService $mavenRepositoryService,
        MavenRepositoryGroupService $mavenRepositoryGroupService,
        EngineInterface $templatingEngine
    ) {
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->templatingEngine = $templatingEngine;
        $this->mavenRepositoryGroupService = $mavenRepositoryGroupService;
        $this->securityService = $securityService;
    }

    public function index(): Response
    {
        return new Response(
            $this->templatingEngine->render(
                'index.html.twig',
                [
                    'mavenRepositories'     => $this->mavenRepositoryService->listReadableRepositories(
                        $this->securityService->findCurrentUser()
                    ),
                    'mavenRepositoryGroups' => $this->mavenRepositoryGroupService->listReadableGroups(
                        $this->securityService->findCurrentUser()
                    )
                ]
            )
        );
    }
}
