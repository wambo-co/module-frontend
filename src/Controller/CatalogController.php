<?php

namespace Wambo\Frontend\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Wambo\Frontend\Exception\ProductNotFoundException;
use Wambo\Frontend\Orchestrator\PageOrchestrator;
use Wambo\Frontend\Orchestrator\ProductDetailsOrchestrator;
use Wambo\Frontend\Orchestrator\ProductOverviewOrchestrator;

/**
 * Class CatalogController contains the frontend controller actions for browsing the product catalog.
 *
 * @package Wambo\Frontend\Controller
 */
class CatalogController
{
    /** @var Twig $renderer */
    private $renderer;

    /** @var ErrorController $errorController */
    private $errorController;

    /** @var PageOrchestrator */
    private $pageOrchestrator;

    /** @var ProductOverviewOrchestrator */
    private $productOverviewOrchestrator;

    /** @var ProductDetailsOrchestrator */
    private $productDetailsOrchestrator;

    /**
     * Creates a new instance of the CatalogController class.
     *
     * @param ContainerInterface $container The slim di container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->errorController = $container->get('errorController');
        $this->renderer = $container->get('renderer');

        // view model orchestrators
        $this->pageOrchestrator = $container->get('pageOrchestrator');
        $this->productOverviewOrchestrator = $container->get('productOverviewOrchestrator');
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
    public function overview(Request $request, Response $response, array $args)
    {
        $pageViewModel = $this->pageOrchestrator->getPageModel("Overview");
        $overviewViewModel = $this->productOverviewOrchestrator->getProductOverviewModel();

        return $this->renderer->render($response, 'overview.html', [
            "page" => $pageViewModel,
            "overview" => $overviewViewModel,
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
    public function productDetails(Request $request, Response $response, array $args)
    {
        /** @var string $slug */
        $slug = $request->getAttribute('slug');

        try {
            $productViewModel = $this->productDetailsOrchestrator->getProductDetailsModel($slug);

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

        } catch (ProductNotFoundException $productNotFoundException) {
            return $this->errorController->error404($request, $response, $args);
        }
    }
}