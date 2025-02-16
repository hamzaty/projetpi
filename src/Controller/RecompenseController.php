<?php

namespace App\Controller;

use App\Entity\Recompense;
use App\Form\Recompense1Type;
use App\Form\RecompenseType;
use App\Repository\RecompenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recompense')]
final class RecompenseController extends AbstractController
{
    #[Route(name: 'app_recompense_index', methods: ['GET'])]
    public function index(RecompenseRepository $recompenseRepository): Response
    {
        return $this->render('recompense/index.html.twig', [
            'recompenses' => $recompenseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recompense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recompense = new Recompense();
        $form = $this->createForm(RecompenseType::class, $recompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            // Check if an image was uploaded
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Move the uploaded file to the directory where images are stored
                $imageFile->move(
                    $this->getParameter('images_directory'), // The directory where images will be stored, defined in config/services.yaml
                    $newFilename
                );

                // Update the 'image' field with the filename
                $recompense->setImage($newFilename);
            }
            $entityManager->persist($recompense);
            $entityManager->flush();

            return $this->redirectToRoute('app_recompense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recompense/new.html.twig', [
            'recompense' => $recompense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recompense_show', methods: ['GET'])]
    public function show(Recompense $recompense): Response
    {
        return $this->render('recompense/show.html.twig', [
            'recompense' => $recompense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recompense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recompense $recompense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecompenseType::class, $recompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recompense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recompense/edit.html.twig', [
            'recompense' => $recompense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recompense_delete', methods: ['POST'])]
    public function delete(Request $request, Recompense $recompense, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recompense->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($recompense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recompense_index', [], Response::HTTP_SEE_OTHER);
    }
}
