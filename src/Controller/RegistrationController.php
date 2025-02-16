<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ClientRegistrationFormType;
use App\Form\AdminRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register/client', name: 'app_register_client')]
    public function registerClient(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(ClientRegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData()); // Vérifiez les données du formulaire
            dump($user); // Vérifiez l'entité User avant la persistance
        
            // Hash du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
        
            // Définir le rôle comme ROLE_CLIENT
            $user->setRoles(['ROLE_CLIENT']);
        
            // Enregistrer les champs supplémentaires pour les clients
            $user->setName($form->get('name')->getData());
            $user->setAdresse($form->get('adresse')->getData());
            $user->setPhonenumber($form->get('phonenumber')->getData());
        
            dump($user); // Vérifiez l'entité User après l'assignation des données
        
            // Persist et flush l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();
        
            $this->addFlash('success', 'Votre inscription en tant que client a été réussie !');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register_client.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/admin', name: 'app_register_admin')]
    public function registerAdmin(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(AdminRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set the role to ROLE_ADMIN
            $user->setRoles(['ROLE_ADMIN']);

            // Persist the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to login page with a success message
            $this->addFlash('success', 'Votre inscription en tant qu\'administrateur a été réussie !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_admin.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}