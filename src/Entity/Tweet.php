<?php

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=280)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="tweets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity=user::class, inversedBy="retweets")
     */
    private $retweets;

    public function __construct()
    {
        $this->retweets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?user
    {
        return $this->author;
    }

    public function setAuthor(?user $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getRetweets(): Collection
    {
        return $this->retweets;
    }

    public function addRetweet(user $retweet): self
    {
        if (!$this->retweets->contains($retweet)) {
            $this->retweets[] = $retweet;
        }

        return $this;
    }

    public function removeRetweet(user $retweet): self
    {
        $this->retweets->removeElement($retweet);

        return $this;
    }
}
