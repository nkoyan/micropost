<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @Route("/", name="micro_post_index")
     */
    public function index(MicroPostRepository $microPostRepository, UserRepository $userRepository)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $usersToFollow = [];

        if ($currentUser) {
            $posts = $microPostRepository->findAllByUsers($currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser) : [];
        } else {
            $posts = $microPostRepository->findBy([], ['time' => 'DESC']);
        }


        return $this->render('micro-post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', microPost)", message="Access Denied.")
     */
    public function edit(Request $request, MicroPost $microPost)
    {
        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $userWithPosts, MicroPostRepository $repo)
    {
        return $this->render('micro-post/user-posts.html.twig', [
            'posts' => $repo->findBy(['user' => $userWithPosts], ['time' => 'DESC']),
            'user' => $userWithPosts
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="micro_post_show")
     */
    public function show(MicroPost $microPost)
    {
        return $this->render('micro-post/show.html.twig', [
            'post' => $microPost
        ]);
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', microPost)", message="Access Denied.")
     */
    public function delete(MicroPost $microPost)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($microPost);
        $em->flush();

        $this->addFlash('success', "Micro post deleted");

        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();

        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($microPost);
            $em->flush();

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}