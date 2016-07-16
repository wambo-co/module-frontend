<?php

namespace Wambo\Frontend;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slim\Views\PhpRenderer;
use Wambo\Catalog\CachedProductRepository;
use Wambo\Catalog\Mapper\ContentMapper;
use Wambo\Catalog\Mapper\ProductMapper;
use Wambo\Catalog\ProductRepository;
use Wambo\Core\App;
use Wambo\Core\Module\JSONModuleStorage;
use Wambo\Core\Module\ModuleBootstrapInterface;
use Wambo\Frontend\ViewModel\Catalog;
use Stash\Pool;

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

        // get the products from the cached repository
        $products = $cachedProductRepository->getProducts();

        $viewModel = new Catalog();
        $viewModel->Products = $products;

        // Get container
        $container = $app->getContainer();

        $container['model'] = $viewModel;

        // Register component on container
        $container['view'] = function ($container) {
            $path = realpath(dirname(__FILE__) . '/../view') . '/';
            return new PhpRenderer($path);
        };

        $app->get('/', function ($request, $response, $args) {
            return $this->view->render($response, 'catalog.php', [
                'name' => $args['name'],
                "viewModel" => $this->model
            ]);
        });

        $app->get('/product/{slug}', function ($request, $response, $args) {
            $slug = $request->getAttribute('slug');
            return $this->view->render($response, 'catalog.php', [
                'name' => $args['name'],
                "viewModel" => $this->model
            ]);
        });
    }
}
