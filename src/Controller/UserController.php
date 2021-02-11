<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{username}", name="user")
     */
    public function index(string $username): Response
    {
        
        
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->render('404.html.twig');
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'tweets' => $user->getTweets(),
        ]);
    }
}