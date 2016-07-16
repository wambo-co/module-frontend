<?php
/** @var $viewModel \Wambo\Frontend\ViewModel\Catalog */
$products = $viewModel->Products;
?>

<!-- Products -->
<?php  foreach ($products as $product):
    /** @var \Wambo\Catalog\Model\Product $product */
?>
    <div class="product">
        <header><h2><a href="/product/<?= $product->getSlug() ?>"><?= $product->getTitle() ?></a></h2></header>
        <section title="summary"><?= $product->getSummaryText() ?></section>
        <section title="description"><?= $product->getProductDescription() ?></section>
    </div>
<?php endforeach; ?>
