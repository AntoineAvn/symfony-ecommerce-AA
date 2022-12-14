<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\BrandRepository;
use App\Repository\CartsProductsRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContentController extends AbstractController
{

    /********************************** HOME ******************************************/

    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findBy(
            ['status' => 1],
            ['sold' => 'DESC'],
            3,
            null
        );

        return $this->render('content/home.html.twig', [
            'products' => $products,
        ]);
    }

    /********************************** PRODUCT ******************************************/

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function indexProducts(ProductRepository $productRepository): Response
    {
        return $this->render('content/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_read', methods: ['GET'])]
    public function readProduct(Product $product): Response
    {
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /********************************** CATEGORY ******************************************/

    #[Route('/categories', name: 'app_categories', methods: ['GET'])]
    public function indexCategories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }


    #[Route('/category/{id}', name: 'app_category_read', methods: ['GET'])]
    public function readCategory(Category $category): Response
    {
        return $this->render('content/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /********************************** BRAND ******************************************/

    #[Route('/brand', name: 'app_brands', methods: ['GET'])]
    public function indexBrands(BrandRepository $brandRepository): Response
    {
        return $this->render('content/brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),
        ]);
    }

    #[Route('/brand/{id}', name: 'app_brand_read', methods: ['GET'])]
    public function readBrand(Brand $brand): Response
    {
        return $this->render('content/brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }
    
}
