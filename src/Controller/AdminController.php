<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('/baseAdmin.html.twig'); // Adjust this path if necessary
    }

    // Create a new user
    #[Route('/admin/user/create', name: 'admin_user_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encrypt password before saving using the correct method
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the user index page after creating
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   // Edit a user
#[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
#[IsGranted('ROLE_ADMIN')]
public function editUser(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    // Create the form for the User entity
    $form = $this->createForm(UserType::class, $user);

    // Handle the form submission
    $form->handleRequest($request);

    // If the form is submitted and valid, process the data
    if ($form->isSubmitted() && $form->isValid()) {
        // If the password was modified, hash it before saving
        if ($user->getPassword()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        // Save changes to the database
        $entityManager->flush();

        // Redirect to the user index page after editing
        $this->addFlash('success', 'User updated successfully!'); // Optional flash message for user feedback
        return $this->redirectToRoute('admin_user_index');
    }

    // Render the edit user form
    return $this->render('admin/user/edit.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}


    // List all users (Read operation)
    #[Route('/admin/user', name: 'admin_user_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function indexUser(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    // Delete a user
    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        // Redirect to the user index page after deletion
        $this->addFlash('success', 'User deleted successfully!');

        return $this->redirectToRoute('admin_user_index');
    }
    #[Route('/search/users', name: 'search_users', methods: ['GET'])]
    public function searchUsers(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->query->get('query');
        
        if (!$query) {
            return new JsonResponse(['error' => 'No search query provided'], 400);
        }

        $users = $userRepository->createQueryBuilder('u')
            ->where('u.username LIKE :query OR u.email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return new JsonResponse($users);
    }
}
