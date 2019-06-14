<?php

namespace App\Controller;

use App\Security\CurrentUserTrait;
use App\Service\MavenRepositoryGroupService;
use App\Service\MavenRepositoryService;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class IndexController
{
    use CurrentUserTrait;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var MavenRepositoryService
     */
    private $mavenRepositoryService;

    /**
     * @var TwigEngine
     */
    private $templatingEngine;

    /**
     * @var MavenRepositoryGroupService
     */
    private $mavenRepositoryGroupService;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        MavenRepositoryService $mavenRepositoryService,
        MavenRepositoryGroupService $mavenRepositoryGroupService,
        EngineInterface $templatingEngine
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->mavenRepositoryService = $mavenRepositoryService;
        $this->templatingEngine = $templatingEngine;
        $this->mavenRepositoryGroupService = $mavenRepositoryGroupService;
    }

    public function index()
    {
        return new Response(
            $this->templatingEngine->render(
                'index.html.twig',
                [
                    'mavenRepositories'     => $this->mavenRepositoryService->listReadableRepositories(
                        $this->findCurrentUser()
                    ),
                    'mavenRepositoryGroups' => $this->mavenRepositoryGroupService->listReadableGroups(
                        $this->findCurrentUser()
                    )
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }
}
