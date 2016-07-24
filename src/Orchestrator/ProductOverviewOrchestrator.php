<?php

namespace Wambo\Frontend\Orchestrator;

use Interop\Container\ContainerInterface;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Frontend\ViewModel\ProductOverview;

/**
 * Class ProductOverviewOrchestrator provides view models for product overview pages.
 *
 * @package Wambo\Frontend\Orchestrator
 */
class ProductOverviewOrchestrator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductDetailsOrchestrator */
    private $productDetailsOrchestrator;

    /**
     * Creates a new instance of the ProductOverviewOrchestrator class.
     *
     * @param ContainerInterface $container The slim di container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->productRepository = $container->get('productRepository');
        $this->productDetailsOrchestrator = $container->get('productDetailsOrchestrator');
    }

    /**
     * Get a product overview view model
     *
     * @return ProductOverview
     */
    public function getProductOverviewModel(): ProductOverview
    {
        $products = $this->productRepository->getProducts();

        $productModels = [];
        foreach ($products as $product) {
            $productDetailsModel = $this->productDetailsOrchestrator->getProductDetailsModel($product->getSlug());
            $productModels[] = $productDetailsModel;
        }

        $viewModel = new ProductOverview();
        $viewModel->products = $productModels;

        return $viewModel;
    }
}