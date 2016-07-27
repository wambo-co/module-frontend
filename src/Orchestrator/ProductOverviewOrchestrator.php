<?php

namespace Wambo\Frontend\Orchestrator;

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
     * @param ProductRepositoryInterface $productRepository
     * @param ProductDetailsOrchestrator $productDetailsOrchestrator
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductDetailsOrchestrator $productDetailsOrchestrator
    ) {
        $this->productRepository = $productRepository;
        $this->productDetailsOrchestrator = $productDetailsOrchestrator;
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