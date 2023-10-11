<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Seller;
use App\Entity\Like;
use App\Form\CommentsType;
use App\Form\SellerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/seller')]
class
CommentsController extends AbstractController
{
    #[Route('/', name: 'app_seller_index', methods: ['GET'])]
    public function index(Comments $commentsRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('#', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/new.html.twig', [
            'comments' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seller $seller, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $seller->getId(), $request->request->get('_token'))) {
            $entityManager->remove($seller);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }


    
}
