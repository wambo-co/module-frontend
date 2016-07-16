<?php
/** @var $viewModel \Wambo\Frontend\ViewModel\ProductDetails */
$product = $viewModel->Product;
?>

<!-- Product -->
<div class="product">
    <header><h2><?= $product->getTitle() ?></h2></header>
    <section title="summary"><?= $product->getSummaryText() ?></section>
    <section title="description"><?= $product->getProductDescription() ?></section>
</div>