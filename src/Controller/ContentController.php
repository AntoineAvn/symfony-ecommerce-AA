<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContentController extends AbstractController
{

    /********************************** HOME ******************************************/

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('content/home.html.twig', [
            'controller_name' => 'ContentController',
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
    public function show(Category $category): Response
    {
        return $this->render('content/category/show.html.twig', [
            'category' => $category,
        ]);
    }
    
}
