<?php

namespace Wambo\Frontend;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slim\Views\PhpRenderer;
use Wambo\Catalog\CachedProductRepository;
use Wambo\Catalog\Mapper\ContentMapper;
use Wambo\Catalog\Mapper\ProductMapper;
use Wambo\Catalog\Model\Product;
use Wambo\Catalog\ProductRepository;
use Wambo\Catalog\ProductRepositoryInterface;
use Wambo\Core\App;
use Wambo\Core\Module\JSONModuleStorage;
use Wambo\Core\Module\ModuleBootstrapInterface;
use Wambo\Frontend\ViewModel\Catalog;
use Stash\Pool;
use Wambo\Frontend\ViewModel\ProductDetails;

/**
 * Class Frontend integrates this module into Wambo application.
 *
 * @package Wambo\Frontend
 */
class Frontend implements ModuleBootstrapInterface
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        // Get container
        $container = $app->getContainer();
        $container["productRepository"] = $this->getProductRepository();

        // Register component on container
        $container['renderer'] = function ($container) {
            $path = realpath(dirname(__FILE__) . '/../view') . '/';
            return new PhpRenderer($path);
        };

        // overview
        $app->get('/', 'Wambo\Frontend\Controller\CatalogController:overview');

        // product details
        $app->get('/product/{slug}', function ($request, $response, $args) {

            /** @var string $slug */
            $slug = $request->getAttribute('slug');

            /** @var ProductRepositoryInterface $productRepository */
            $productRepository = $this->repository;

            // get the products from the cached repository
            $product = null;
            $products = $productRepository->getProducts();

            foreach ($products as $p) {
                /** @var Product $product */
                if ($p->getSlug()->__toString() === $slug) {
                    $product = $p;
                    break;
                }

            }

            // create a view model
            $viewModel = new ProductDetails();
            $viewModel->Title = $product->getTitle();
            $viewModel->Product = $product;

            return $this->view->render($response, 'product.php', [
                'name' => $args['name'],
                "viewModel" => $viewModel
            ]);
        });
    }

    /**
     * Get a product repository instance
     *
     * @return ProductRepositoryInterface
     */
    private function getProductRepository()
    {
        // catalog storage
        $sampleCatalogFilename = "sample-catalog.json";
        $testResourceFolderPath = realpath(WAMBO_ROOT_DIR . "/vendor/wambo/module-catalog/examples/" . '/catalog');

        $localFilesystemAdapter = new Local($testResourceFolderPath);
        $filesystem = new Filesystem($localFilesystemAdapter);
        $storage = new JSONModuleStorage($filesystem, $sampleCatalogFilename);

        // product mapper
        $contentMapper = new ContentMapper();
        $productMapper = new ProductMapper($contentMapper);

        // create the product repository
        $productRepository = new ProductRepository($storage, $productMapper);

        // create a cached version of the product repository
        $cache = new Pool();
        $cachedProductRepository = new CachedProductRepository($cache, $productRepository);
        return $cachedProductRepository;
    }
}
