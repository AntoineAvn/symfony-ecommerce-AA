<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    public function indexProducts(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $products = $productRepository->findAll();

        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('content/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_read', methods: ['GET'])]
    public function readProduct(Product $product): Response
    {
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
