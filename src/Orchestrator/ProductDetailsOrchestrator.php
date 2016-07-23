<?php

namespace Wambo\Frontend\Orchestrator;

use Interop\Container\ContainerInterface;
use Wambo\Catalog\Model\Product;
use Wambo\Frontend\ViewModel\ProductDetails;

/**
 * Class ProductDetailsOrchestrator creates ProductDetail view models from Product models.
 *
 * @package Wambo\Frontend\Orchestrator
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
    public function getProductDetailsModel(Product $productModel): ProductDetails
    {
        $productViewModel = new ProductDetails();
        $productViewModel->sku = $productModel->getSku()->__toString();
        $productViewModel->title = $productModel->getTitle();
        $productViewModel->slug = $productModel->getSlug()->__toString();
        $productViewModel->summary = $productModel->getSummaryText();
        $productViewModel->description = $productModel->getProductDescription();

        return $productViewModel;
    }
}