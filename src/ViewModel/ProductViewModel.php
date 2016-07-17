<?php

namespace Wambo\Frontend\ViewModel;

use Wambo\Catalog\Model\Product;

/**
 * Class ProductViewModel defines the view model attributes of a product details page
 *
 * @package Wambo\Frontend\ViewModel
 */
class ProductViewModel extends PageViewModel
{
    /** @var Product $Product */
    public $Product;
}