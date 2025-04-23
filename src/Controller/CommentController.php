<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comments', name: 'app_comments')]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }


    #[Route('/comment/edit/{id}', name: 'app_comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$comment){
            return $this->redirectToRoute('app_posts_show', ['id' => $comment->getPost()->getId()], Response::HTTP_BAD_REQUEST);
        }
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('app_posts_show', ['id' => $comment->getPost()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'commentForm' => $commentForm->createView(),
        ]);
    }


    #[Route('/comment/delete/{id}', name: 'app_comment_delete')]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$comment){
            return $this->redirectToRoute('app_posts_show', ['id'=>$comment->getPost()->getId()]);
        }
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('app_posts_show', ['id'=>$comment->getPost()->getId()]);
    }





}
