<?php

namespace Wambo\Frontend\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Frontend\ViewModel\Catalog;

/**
 * Class CatalogController contains the frontend controller actions for browsing the product catalog.
 *
 * @package Wambo\Frontend\Controller
 */
class CatalogController
{
    /** @var ProductRepositoryInterface $productRepository */
    private $productRepository;

    /** @var PhpRenderer $renderer */
    private $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->productRepository = $container->get('productRepository');
        $this->renderer = $container->get('renderer');
    }

    /**
     * Print the catalog overview pages with all products on one page
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return ResponseInterface
     */
    public function overview(Request $request, Response $response, $args)
    {
        // get the products from the cached repository
        $products = $this->productRepository->getProducts();

        // create a view model
        $viewModel = new Catalog();
        $viewModel->Products = $products;

        return $this->renderer->render($response, 'catalog.php', [
            'name' => $args['name'],
            "viewModel" => $viewModel
        ]);
    }
}