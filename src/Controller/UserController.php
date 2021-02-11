<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FollowType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{username}", name="user")
     */
    public function index(string $username, Request $request): Response
    {
        
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        $form = $this->createForm(FollowType::class);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $currentUser->addFollow($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($currentUser);
            $entityManager->flush();
            return $this->redirectToRoute('user', ['username' => $username]);
        }
            
        if (!$user) {
            return $this->render('404.html.twig');
        }
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'tweets' => $user->getTweets(),
            'followForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/user/{username}/followers", name="user_followers")
     */
    public function followers(string $username): Response
    {
        
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
            
        if (!$user) {
            return $this->render('404.html.twig');
        }
        
        return $this->render('user/listUser.html.twig', [
            'user' => $user,
            'list' => $user->getFollowers(),
            'type' => 'followers'
        ]);
    }

    /**
     * @Route("/user/{username}/follows", name="user_follows")
     */
    public function follows(string $username): Response
    {
        
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
            
        if (!$user) {
            return $this->render('404.html.twig');
        }
        
        return $this->render('user/listUser.html.twig', [
            'user' => $user,
            'list' => $user->getFollow(),
            'type' => 'follows'
        ]);
    }
}