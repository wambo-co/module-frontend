<?php

namespace Wambo\Frontend\Controller;

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
     * @param ErrorController             $errorController
     * @param Twig                        $renderer
     * @param PageOrchestrator            $pageOrchestrator
     * @param ProductOverviewOrchestrator $productOverviewOrchestrator
     * @param ProductDetailsOrchestrator  $productDetailsOrchestrator
     */
    public function __construct(
        ErrorController $errorController,
        Twig $renderer,
        PageOrchestrator $pageOrchestrator,
        ProductOverviewOrchestrator $productOverviewOrchestrator,
        ProductDetailsOrchestrator $productDetailsOrchestrator
    ) {
        $this->errorController = $errorController;
        $this->renderer = $renderer;

        // view model orchestrators
        $this->pageOrchestrator = $pageOrchestrator;
        $this->productOverviewOrchestrator = $productOverviewOrchestrator;
        $this->productDetailsOrchestrator = $productDetailsOrchestrator;
    }

    /**
     * Render the catalog overview pages with all products on one page
     *
     * @param Response $response The response object
     *
     * @return ResponseInterface
     */
    public function overview(Response $response)
    {
        $pageViewModel = $this->pageOrchestrator->getPageModel(
            "Overview",
            "Product overview"
        );

        $overviewViewModel = $this->productOverviewOrchestrator->getProductOverviewModel();

        return $this->renderer->render($response, 'overview.twig', [
            "page" => $pageViewModel,
            "overview" => $overviewViewModel,
        ]);
    }

    /**
     * Render the product details page.
     *
     * @param string   $slug
     * @param Request  $request
     * @param Response $response The response object
     *
     * @return ResponseInterface
     */
    public function productDetails(string $slug, Request $request, Response $response)
    {
        try {
            $productViewModel = $this->productDetailsOrchestrator->getProductDetailsModel($slug);

            $pageViewModel = $this->pageOrchestrator->getPageModel(
                $productViewModel->title,
                $productViewModel->description,
                $productViewModel->uri
            );

            $viewModel = [
                "page" => $pageViewModel,
                "product" => $productViewModel
            ];

            return $this->renderer->render($response, 'product.twig', $viewModel);

        } catch (ProductNotFoundException $productNotFoundException) {
            return $this->errorController->error404($request, $response);
        }
    }
}