<?php

namespace App\Controller;

use App\Entity\Retweet;
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
        $user = $this->getUser();

        $tweetList = [];
        $tweetList = array_merge($tweetList, $user->getTweets()->toArray());
        foreach ($user->getRetweets()->toArray() as $key => $retweet) {
            $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $retweet->getTweet()]);

            $isFollowed = false;
            foreach ($user->getFollow() as $key => $follow) {
                if ($follow->getId() ===  $tweet->getAuthor()->getId()) {
                    $isFollowed = true;
                }
            }
            if (!$isFollowed) {
                $tweetItem[] = $tweet;
                $tweetList = array_merge($tweetList, $tweetItem);
            }
        }


        foreach ($user->getFollow() as $key => $follow) {
            $tweetList = array_merge($tweetList, $follow->getTweets()->toArray());
            foreach ($follow->getRetweets()->toArray() as $key => $retweet) {
                $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $retweet->getTweet()]);

                $isFollowed = false;
                foreach ($user->getFollow() as $key => $follow) {
                    if ($follow->getId() ===  $tweet->getAuthor()->getId()) {
                        $isFollowed = true;
                    }
                }
                if (!$isFollowed) {
                    $tweetItem[] = $tweet;
                    $tweetList = array_merge($tweetList, $tweetItem);
                }
            }
        }

        $tweet = new Tweet();

        $form = $this->createForm(TweetType::class, $tweet);

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
            'createTweet' => $form->createView(),
            'tweets' => $tweetList,
        ]);
    }


    /**
     * @Route("/retweet/{tweet_id}", name="retweet")
     */
    public function retweet(string $tweet_id, Request $request): Response
    {
        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $tweet_id]);

        $retweet = new Retweet();
        $retweet->setCreatedAt(new \DateTime());
        $retweet->setUser($this->getUser());
        $retweet->setTweet($tweet);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($retweet);
        $entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/retweet-remove/{retweet_id}", name="retweet_remove")
     */
    public function remove_retweet(string $retweet_id, Request $request): Response
    {
        $retweet = $this->getDoctrine()->getRepository(Retweet::class)->find($retweet_id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($retweet);
        $entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/tweet/{tweet_id}/retweeters", name="retweeters")
     */
    public function retweeters(string $tweet_id): Response
    {
        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $tweet_id]);

        $retweets = $tweet->getRetweets();

        return $this->render('tweet/retweeter.html.twig', [
            'retweets' => $retweets,
        ]);
    }
}
