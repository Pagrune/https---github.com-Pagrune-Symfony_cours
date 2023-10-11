<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Like;
use App\Entity\Comment;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Types\Types;
use App\Repository\LikeRepository;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blog' => $blogRepository->findAll(),
        ]);
    }

    #[Route('/blog/{id}', name: 'detail_blog')]
    public function show(Blog $blog, $id, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentsRepository): Response
    {
        $comment = new Comment();
        $form = $this->CreateForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setPost($blog);

            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('detail_blog', [
                    'id' => $id
                ]
            );
        }
        return $this->render('blog/post.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    #[Route('/blog/{id}/{comment}', name: 'like_comment')]
    public function liked($id, $comment, EntityManagerInterface $entityManager, CommentRepository $commentsRepository, LikeRepository $likeRepository) 
    {
    // Récupérer l'entité Comment à partir de l'ID (converti en entier)
    $commentEntity = $commentsRepository->find((int) $comment);
    
    // Récupérer l'utilisateur actuel
    $user = $this->getUser();

    // Rechercher le like spécifique de l'utilisateur sur ce commentaire
    $existingLike = $likeRepository->findOneBy([
        'idlikeuser' => $user,
        'idlikecomments' => $commentEntity,
    ]);

    // Si l'utilisateur a déjà aimé ce commentaire, supprimer le like
    if ($existingLike) {
        return $this->redirectToRoute('detail_blog', [
        'id' => $id,
    ]);
    } 
    else {
        // Si l'utilisateur n'a pas encore aimé ce commentaire, créer un nouveau like
        $like = new Like();
        $like->setIdlikeuser($user)
            ->setIdlikecomments($commentEntity);

        $entityManager->persist($like);
        $entityManager->flush();
    }

    return $this->redirectToRoute('detail_blog', [
        'id' => $id,
    ]);
    }


}
