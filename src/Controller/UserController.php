<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use App\Form\FollowType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private function addTweetToTweetLine($tweetList)
    {
        $tweets = [];
        foreach ($tweetList as $tweet) {
            $tweetItem[] = [$tweet, null, $tweet->getCreatedAt()];
            $tweets = array_merge($tweets, $tweetItem);
            $tweetItem = null;
        }
        return $tweets;
    }

    private function addRetweetToTweetLine($retweets)
    {
        $retweetItem = [];
        foreach ($retweets as $retweet) {
            $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $retweet->getTweet()]);
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $retweet->getUser()]);
            $createdAt = $retweet->getCreatedAt();
            $retweetItem[] = [$tweet, $user, $createdAt];
        }
        return $retweetItem;
    }

    /**
     * @Route("/user/{username}", name="user")
     */
    public function index(string $username, Request $request): Response
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        $TweetLine = [];

        $TweetLine = array_merge($TweetLine, $this->addTweetToTweetLine($user->getTweets()->toArray()));
        $TweetLine = array_merge($TweetLine, $this->addRetweetToTweetLine($user->getRetweets()->toArray()));


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
            'tweets' => $TweetLine,
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
