<?php

namespace Wambo\Frontend\ViewModel;

/**
 * Class ProductDetails is a view model for a product details page
 *
 * @package Wambo\Frontend\ViewModel
 */
class ProductDetails
{
    /** @var string $sku The product SKU */
    public $sku;

    /** @var string $uri */
    public $uri;

    /** @var string $title The product title */
    public $title;

    /** @var string $summary The product summary or short description */
    public $summary;

    /** @var string $description The product description */
    public $description;
}