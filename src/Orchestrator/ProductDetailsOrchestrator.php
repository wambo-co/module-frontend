<?php

namespace Wambo\Catalog\Orchestrator;

use Wambo\Catalog\Model\Product;
use Wambo\Catalog\ViewModel\ProductDetails;

/**
 * Class ProductDetailsOrchestrator creates ProductDetail view models from Product models.
 *
 * @package Wambo\Catalog\Orchestrator
 */
class ProductDetailsOrchestrator
{
    /**
     * Get a ProductDetails view model for the given product model
     *
     * @param Product $productModel A product model
     *
     * @return ProductDetails
     */
    public function getProductDetails(Product $productModel): ProductDetails
    {
        $viewModel = new ProductDetails();

        $viewModel->sku = $productModel->getSku()->__toString();
        $viewModel->title = $productModel->getTitle();
        $viewModel->slug = $productModel->getSlug()->__toString();
        $viewModel->summary = $productModel->getSummaryText();
        $viewModel->description = $productModel->getProductDescription();

        return $viewModel;
    }
}