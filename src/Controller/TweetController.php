<?php

namespace App\Controller;

use App\Entity\Retweet;
use App\Entity\Tweet;
use App\Entity\User;
use App\Form\SearchType;
use App\Form\TweetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetController extends AbstractController
{

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        $request = $request->request->get('search');

        return $this->redirect('/user/' . $request);
    }


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

    private function addRetweetToTweetLine($retweet, $follow = null)
    {
        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->findOneBy(['id' => $retweet->getTweet()]);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $retweet->getUser()]);
        $createdAt = $retweet->getCreatedAt();
        $tweetItem[] = [$tweet, $user, $createdAt];
        // verifie que le retweet n'est pas un tweet de l'utilisateur ou un follow
        if ($tweet->getAuthor() !== $this->getUser() && $tweet->getAuthor() !== $follow) {
            return $tweetItem;
        } else {
            return [];
        }
    }

    /**
     * @Route("/tweet", name="tweet")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();


        $TweetLine = [];

        // Recupere les tweets et retweets de l'utilisateur
        $TweetLine = array_merge($TweetLine, $this->addTweetToTweetLine($user->getTweets()->toArray()));
        foreach ($user->getRetweets()->toArray() as $key => $retweet) {
            foreach ($user->getFollow() as $key => $follow) {
                $TweetLine = array_merge($TweetLine, $this->addRetweetToTweetLine($retweet, $follow));
            }
        }

        // Recupere les tweets des follows 
        foreach ($user->getFollow() as $key => $follow) {
            $TweetLine = array_merge($TweetLine, $this->addTweetToTweetLine($follow->getTweets()->toArray()));

            // Recupere les retweets des follows
            foreach ($follow->getRetweets() as $key => $retweet) {
                $TweetLine = array_merge($TweetLine, $this->addRetweetToTweetLine($retweet));
            }
        }

        // TWEET

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
            'tweets' => $TweetLine,
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
