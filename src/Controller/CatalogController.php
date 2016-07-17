<?php

namespace Wambo\Frontend\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Wambo\Catalog\Model\Product;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Frontend\ViewModel\OverviewViewModel;
use Wambo\Frontend\ViewModel\ProductViewModel;

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

    /** @var ErrorController $errorController */
    private $errorController;

    /**
     * Creates a new instance of the CatalogController class.
     *
     * @param ContainerInterface $container The slim di container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->productRepository = $container->get('productRepository');
        $this->renderer = $container->get('renderer');
        $this->errorController = $container->get('errorController');
    }

    /**
     * Render the catalog overview pages with all products on one page
     *
     * @param Request  $request  The request object
     * @param Response $response The response object
     * @param array    $args     The request arguments
     *
     * @return ResponseInterface
     */
    public function overview(Request $request, Response $response, $args)
    {
        // get the products from the cached repository
        $products = $this->productRepository->getProducts();

        // create a view model
        $viewModel = new OverviewViewModel();
        $viewModel->Products = $products;

        return $this->renderer->render($response, 'overview.php', [
            'name' => $args['name'],
            "viewModel" => $viewModel
        ]);
    }

    /**
     * Render the product details page.
     *
     * @param Request  $request  The request object
     * @param Response $response The response object
     * @param array    $args     The request arguments
     *
     * @return ResponseInterface
     */
    public function productDetails(Request $request, Response $response, $args)
    {
        // get the product that matches the given slug
        /** @var string $slug */
        $slug = $request->getAttribute('slug');
        $product = $this->getProductBySlug($slug);

        // product not found
        if (is_null($product)) {
            return $this->errorController->error404($request, $response, $args);
        }

        // create a view model
        $viewModel = new ProductViewModel();
        $viewModel->Title = $product->getTitle();
        $viewModel->Product = $product;

        return $this->renderer->render($response, 'product.php', [
            'name' => $args['name'],
            "viewModel" => $viewModel
        ]);
    }

    /**
     * Get the product which belongs to the given slug.
     *
     * @param string $slug
     *
     * @return null|\Wambo\Catalog\Model\Product
     */
    private function getProductBySlug(string $slug)
    {
        // get the products from the cached repository
        $products = $this->productRepository->getProducts();

        foreach ($products as $product) {
            /** @var Product $product */
            if ($product->getSlug()->__toString() === $slug) {
                return $product;
            }
        }

        return null;
    }
}