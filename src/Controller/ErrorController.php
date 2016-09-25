<?php

namespace Wambo\Frontend\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Wambo\Frontend\Orchestrator\PageOrchestrator;

/**
 * Class ErrorController container the frontend controller actions for HTTP errors
 *
 * @package Wambo\Frontend\Controller
 */
class ErrorController
{
    /** @var Twig $renderer */
    private $renderer;

    /** @var PageOrchestrator */
    private $pageOrchestrator;

    /**
     * Creates a new instance of the ErrorController class.
     *
     * @param PageOrchestrator $pageOrchestrator
     * @param Twig             $renderer
     */
    public function __construct(PageOrchestrator $pageOrchestrator, Twig $renderer)
    {
        $this->pageOrchestrator = $pageOrchestrator;
        $this->renderer = $renderer;
    }

    /**
     * Render an 404 error page.
     *
     * @param Request  $request  The request object
     * @param Response $response The response object
     *
     * @return ResponseInterface
     */
    public function error404(Request $request, Response $response)
    {
        $pageViewModel = $this->pageOrchestrator->getPageModel("Page not found");

        $viewModel = [
            "page" => $pageViewModel
        ];

        return $this->renderer->render($response->withStatus(404), 'error.twig', $viewModel);
    }
}