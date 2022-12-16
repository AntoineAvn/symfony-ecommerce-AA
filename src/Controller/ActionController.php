<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Brand;
use App\Entity\Product;
use App\Form\BrandType;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Entity\CartsProducts;
use App\Repository\UserRepository;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\CartsProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActionController extends AbstractController
{

    /********************************** PRODUCT ******************************************/

    #[Route('/product/create', name: 'app_product_create', methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}/update', name: 'app_product_update', methods: ['GET', 'POST'])]
    public function updateProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Default field (updatedAt)
            $product->setUpdateAt(new \DateTimeImmutable());

            //Persist and flush
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
    }

    /********************************** CATEGORY ******************************************/

    #[Route('/category/create', name: 'app_category_create', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}/update', name: 'app_category_update', methods: ['GET', 'POST'])]
    public function updateCategory(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
    }

    /********************************** BRAND ******************************************/

    #[Route('/brand/create', name: 'app_brand_create', methods: ['GET', 'POST'])]
    public function createBrand(Request $request, BrandRepository $brandRepository): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brands', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    

    #[Route('/brand/{id}/update', name: 'app_brand_update', methods: ['GET', 'POST'])]
    public function updateBrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brands', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/brand/delete/{id}', name: 'app_brand_delete', methods: ['POST'])]
    public function deleteBrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $brandRepository->remove($brand, true);
        }

        return $this->redirectToRoute('app_brands', [], Response::HTTP_SEE_OTHER);
    }

    /********************************** CART ******************************************/

    #[Route('/product/add-cart/{id}', name: 'app_product_addCart', methods: ['GET', 'POST'])]
    public function addProductToCart(Request $request, Product $product, CartsProductsRepository $cpRepository): Response
    {
         /** @var User $user **/
         $user = $this->getUser();
         $userId = $this->getUser()->getId();
         $cart = $user->getCart();

         if ($cart) {
            $cp = $cart->getCartsProducts()->toArray();
         
            if(empty($cp)) {
                $cProduct = new CartsProducts();
                $cProduct->setQuantity(1);
                $cProduct->setCart($cart);
                $cProduct->setProduct($product);
            }
            else {
                foreach ($cp as $cProduct) {
                    if ($cProduct->getProduct()->getId() == $product->getId()) {
                        $qty = $cProduct->getQuantity();
                        $cProduct->setQuantity ($qty + 1);
                        break;
                    }
                    else{
                        $cProduct = new CartsProducts();
                        $cProduct->setQuantity(1);
                        $cProduct->setCart($cart);
                        $cProduct->setProduct($product);
                    }
                }
            }
            
            $cpRepository->save($cProduct, true);
         }
         

        return $this->redirectToRoute('app_profile_cart', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/delete-cart/{id}', name: 'app_product_deleteCart', methods: ['GET', 'POST'])]
    public function deleteCart(Product $product, CartsProductsRepository $cpRepository): Response
    {
         /** @var User $user **/
         $user = $this->getUser();
         $userId = $this->getUser()->getId();

         $cart = $user->getCart();
         $cp = $cart->getCartsProducts()->toArray();

        $cart = $user->getCart();
        $cp = $cart->getCartsProducts()->toArray();

        foreach ($cp as $cProduct) {
            // dd($cProduct->getId());
            if ($cProduct->getId() == $product->getId()) {
                $qty = $cProduct->getQuantity();
                // dd($qty);
                $cProduct->setQuantity ($qty - 1);
            }
        }

        $cpRepository->save($cProduct, true);

        return $this->redirectToRoute('app_profile_cart', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/delete-product-cart/{id}', name: 'app_product_deleteProductCart', methods: ['GET', 'POST'])]
    public function deleteProductCart(Product $product, CartsProductsRepository $cpRepository): Response
    {

         /** @var User $user **/
         $user = $this->getUser();
         $userId = $this->getUser()->getId();

         $cart = $user->getCart();
         $cp = $cart->getCartsProducts()->toArray();

        $cart = $user->getCart();
        $cp = $cart->getCartsProducts()->toArray();

        foreach ($cp as $cProduct) {
            if ($cProduct->getId() == $product->getId()) {
                $cpRepository->remove($cProduct, true);
            }
        }

        

        return $this->redirectToRoute('app_profile_cart', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }
}
