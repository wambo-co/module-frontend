<?php

namespace Wambo\Frontend\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Wambo\Catalog\Model\Product;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Frontend\Orchestrator\PageOrchestrator;
use Wambo\Frontend\Orchestrator\ProductDetailsOrchestrator;

/**
 * Class CatalogController contains the frontend controller actions for browsing the product catalog.
 *
 * @package Wambo\Frontend\Controller
 */
class CatalogController
{
    /** @var ProductRepositoryInterface $productRepository */
    private $productRepository;

    /** @var Twig $renderer */
    private $renderer;

    /** @var ErrorController $errorController */
    private $errorController;

    /** @var PageOrchestrator */
    private $pageOrchestrator;

    /** @var ProductDetailsOrchestrator */
    private $productDetailsOrchestrator;

    /**
     * Creates a new instance of the CatalogController class.
     *
     * @param ContainerInterface $container The slim di container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->productRepository = $container->get('productRepository');
        $this->errorController = $container->get('errorController');
        $this->renderer = $container->get('renderer');

        // orchestrators
        $this->pageOrchestrator = $container->get('pageOrchestrator');
        $this->productDetailsOrchestrator = $container->get('productDetailsOrchestrator');
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

        $productModels = [];
        foreach ($products as $product) {
            $productModels[] = $this->getProductModel($product);
        }

        return $this->renderer->render($response, 'overview.html', [
            "title" => "Overview",
            "products" => $productModels,
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

        $productViewModel = $this->productDetailsOrchestrator->getProductDetailsModel($product);

        $pageViewModel = $this->pageOrchestrator->getPageModel(
            $productViewModel->title,
            $productViewModel->description,
            $productViewModel->slug
        );

        $viewModel = [
            "page" => $pageViewModel,
            "product" => $productViewModel
        ];

        return $this->renderer->render($response, 'product.html', $viewModel);
    }

    /**
     * Get a product view model for the given product
     *
     * @param Product $product A product model
     *
     * @return array
     */
    private function getProductModel(Product $product): array
    {
        return [
            "sku" => $product->getSku()->__toString(),
            "title" => $product->getTitle(),
            "slug" => $product->getSlug()->__toString(),
            "summary" => $product->getSummaryText(),
            "description" => $product->getProductDescription(),
        ];
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