<?php

namespace Wambo\Frontend\Controller;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class CartController
{

    /** @var Twig $renderer */
    private $renderer;

    public function __construct(Twig $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $this->renderer->render($response, 'cart.twig', [

        ]);
    }
}