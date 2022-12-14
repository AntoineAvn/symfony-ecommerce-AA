<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Form\UserType;
use App\Form\PasswordFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;



class UserController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_user_read', methods: ['GET'])]
    public function readUser(User $user, ProductRepository $productRepository): Response
    {
        //Get id of user connected to get his projects
        // $user = $userRepository->find($this->getUser());
        $userId = $user->getId();
        $currentUserId = $this->getUser()->getId();

        if ($userId == $currentUserId) {
            $products = $productRepository->findBy(
                ['seller' => $userId],
                ['createdAt' => 'DESC'],
                null,
                null
            );
    
            return $this->render('security/profile/profile.html.twig', [
                'user' => $user,
                'products' => $products,
            ]);
        }
        else {
            return $this->redirectToRoute('app_access_denied', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/profile/{id}/update', name: 'app_user_update', methods: ['GET', 'POST'])]
    public function updateProfile(Request $request, User $user, UserRepository $userRepository): Response
    {

        //Get id of user connected to get his projects
        $user = $userRepository->find($this->getUser());
        $userId = $user->getId();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_read', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/update-password', name: 'app_user_update_password', methods: ['GET', 'POST'])]
    public function updatePassword(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        //Get id of user connected to get his projects
        $user = $userRepository->find($this->getUser());
        $userId = $user->getId();

        $form = $this->createForm(PasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
             $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_read', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cart = new Cart();
            $user->setCart($cart);

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/profile/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/profile/{id}/cart', name: 'app_profile_cart', methods: ['GET'])]
    public function readCart(User $user): Response
    {
        $cart = $user->getCart();

        if ($cart) {
            $products = $cart->getCartsProducts()->toArray();

        $quantities =[];

        foreach ($products as $product) {
            array_push($quantities, $product->getQuantity() * $product->getproduct()->getPrice());
        }

        $totalPrice = array_sum($quantities);

        return $this->render('security/profile/cart.html.twig', [
            'user' => $user,
            'products' => $products,
            'totalPrice' => $totalPrice
        ]);
        }
        
        return $this->render('security/profile/cart.html.twig', [
            'user' => $user
        ]);
    }

    #[Route(path: '/access-denied', name: 'app_access_denied')]
    public function accessDenied()
    {
        return $this->render('security\access_denied.html.twig');
    }
}