<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\ImageType;
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
    public function show(Post $post, EntityManagerInterface $entityManager, Request $request): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_posts_show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $commentForm->createView(),
        ]);
    }


    #[Route('/post/create', name: 'app_posts_create')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
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
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
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
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_posts');
    }

    #[Route('/post/addImage/{id}', name: 'app_post_addImage')]
    public function addImage(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$post)
        {
            return $this->redirectToRoute('app_posts');
        }
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);
        $imageForm->handleRequest($request);
        if($imageForm->isSubmitted() && $imageForm->isValid()){
            $image->setPost($post);
            $entityManager->persist($image);
            $entityManager->flush();
            return $this->redirectToRoute('app_posts_show', ['id' => $post->getId()]);
        }
        return $this->render('post/image.html.twig', [
            'post' => $post,
            'imageForm' => $imageForm->createView(),
        ]);

    }

}
