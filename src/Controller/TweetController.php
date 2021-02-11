<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Form\TweetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TweetController extends AbstractController
{
    /**
     * @Route("/tweet", name="tweet")
     */
    public function index(Request $request): Response
    {

        $tweet = new Tweet();

        $form = $this->createForm(TweetType::class, $tweet);

        $user = $this->getUser();



        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tweet = $form->getData();
            $tweet->setCreatedAt(new \DateTime());
            $tweet->setAuthor($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tweet);
            $entityManager->flush();
            return $this->redirectToRoute('tweet');
        }
        
        return $this->render('tweet/index.html.twig', [
            // 'controller_name' => 'TweetController',
            'form' => $form->createView(),
            'tweets' => $user->getTweets(),



        ]);
    }
}
