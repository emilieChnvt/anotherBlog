<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/show/{id}', name: 'app_posts_show', priority: -1)]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }


    #[Route('/post/create', name: 'app_posts_create')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $post = new Post();
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);
        if($postForm->isSubmitted() && $postForm->isValid()){
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('post/create.html.twig', [
            'postForm' => $postForm->createView(),
        ]);

    }


    #[Route('/post/edit/{id}', name: 'app_posts_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$post)
        {
            return $this->redirectToRoute('app_posts');
        }
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);
        if($postForm->isSubmitted() && $postForm->isValid()){
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/edit.html.twig', [
            'postForm' => $postForm->createView(),
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_posts_delete')]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        if(!$post)
        {
            return $this->redirectToRoute('app_posts');
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_posts');
    }

}
