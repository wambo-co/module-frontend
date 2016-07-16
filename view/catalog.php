<?php
/** @var $viewModel \Wambo\Frontend\ViewModel\Catalog */
$products = $viewModel->Products;
?>

<!-- Products -->
<?php  foreach ($products as $product):
    /** @var \Wambo\Catalog\Model\Product $product */
?>
    <div class="product">
        <header><h2><?= $product->getTitle() ?></h2></header>
        <section title="summary"><?= $product->getSummaryText() ?></section>
        <section title="description"><?= $product->getProductDescription() ?></section>
    </div>
<?php endforeach; ?>
