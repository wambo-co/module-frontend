<?php

namespace Wambo\Frontend\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Wambo\Frontend\ViewModel\ErrorViewModel;

/**
 * Class ErrorController container the frontend controller actions for HTTP errors
 *
 * @package Wambo\Frontend\Controller
 */
class ErrorController
{
    /** @var PhpRenderer $renderer */
    private $renderer;

    /**
     * Creates a new instance of the ErrorController class.
     *
     * @param ContainerInterface $container The slim di container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->renderer = $container->get('renderer');
    }

    /**
     * Render an 404 error page.
     *
     * @param Request  $request  The request object
     * @param Response $response The response object
     * @param array    $args     The request arguments
     *
     * @return ResponseInterface
     */
    public function error404(Request $request, Response $response, $args)
    {
        return $this->renderer->render($response->withStatus(404), 'error.html', [
            "title" => "Page not found"
        ]);
    }
}