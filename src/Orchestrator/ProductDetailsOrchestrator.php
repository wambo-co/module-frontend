<?php

namespace Wambo\Frontend\Orchestrator;

use Wambo\Catalog\Model\Product;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Frontend\Exception\ProductNotFoundException;
use Wambo\Frontend\ViewModel\ProductDetails;

/**
 * Class ProductDetailsOrchestrator provides product details view models for product detail pages.
 *
 * @package Wambo\Frontend\Orchestrator
 */
class ProductDetailsOrchestrator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /**
     * Creates a new instance of the ProductDetailsOrchestrator class.
     *
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get a ProductDetails view model for the given product model
     *
     * @param string $slug
     *
     * @return ProductDetails
     *
     * @throws ProductNotFoundException If no product was found that has the specified $slug
     */
    public function getProductDetailsModel(string $slug): ProductDetails
    {
        $product = $this->getProductBySlug($slug);
        if (is_null($product)) {
            throw new ProductNotFoundException("No product with the slug $slug was found");
        }

        $productViewModel = new ProductDetails();
        $productViewModel->sku = $product->getSku()->__toString();
        $productViewModel->title = $product->getTitle();
        $productViewModel->slug = $product->getSlug()->__toString();
        $productViewModel->summary = $product->getSummaryText();
        $productViewModel->description = $product->getProductDescription();

        return $productViewModel;
    }

    /**
     * Get the product which belongs to the given slug.
     *
     * @param string $slug
     *
     * @return Product
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