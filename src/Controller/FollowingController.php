<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends AbstractController
{
    /**
     * @Route("/follow/{id}", name="following_follow")
     */
    public function follow(User $userToFollow)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser !== $userToFollow) {
            $em = $this->getDoctrine()->getManager();
            $currentUser->follow($userToFollow);
            $em->flush();
        }

        return $this->redirectToRoute('micro_post_user', ['username' => $userToFollow->getUsername()]);
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     */
    public function unfollow(User $userToUnfollow)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $currentUser->unfollow($userToUnfollow);
        $em->flush();

        return $this->redirectToRoute('micro_post_user', ['username' => $userToUnfollow->getUsername()]);
    }
}
