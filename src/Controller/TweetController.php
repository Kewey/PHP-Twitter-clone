<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetController extends AbstractController
{
    /**
     * @Route("/tweet", name="tweet")
     */
    public function index(): Response
    {
        return $this->render('tweet/index.html.twig', [
            'controller_name' => 'TweetController',
        ]);
    }
}
