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
        /** @var User $user **/
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setSeller($user);

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_user_read', ['id' => $userId], Response::HTTP_SEE_OTHER);
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

        foreach ($cp as $cProduct) {
            if ($cProduct->getProduct()->getId() == $product->getId()) {
                $qty = $cProduct->getQuantity();
                $cProduct->setQuantity ($qty - 1);

                //Remove product from cart if quantity is <= 0 
                if ($cProduct->getQuantity() <= 0) {
                    $cProductId = $cProduct->getProduct()->getId();
                    return $this->redirectToRoute('app_product_deleteProductCart', ['id' => $cProductId], Response::HTTP_SEE_OTHER);
                }
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

        foreach ($cp as $cProduct) {
            if ($cProduct->getProduct()->getId() == $product->getId()) {
                $cpRepository->remove($cProduct, true);
            }
        }

        return $this->redirectToRoute('app_profile_cart', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/delete-all-product-cart/', name: 'app_product_deleteAllProductCart', methods: ['GET', 'POST'])]
    public function deleteAllProductCart(CartsProductsRepository $cpRepository): Response
    {

        /** @var User $user **/
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $cart = $user->getCart();
        $cp = $cart->getCartsProducts()->toArray();

        foreach ($cp as $cProduct) {
            $cpRepository->remove($cProduct, true);
        }

        return $this->redirectToRoute('app_profile_cart', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }
}
