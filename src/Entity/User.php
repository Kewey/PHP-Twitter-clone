<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Tweet::class, mappedBy="author", orphanRemoval=true)
     */
    private $tweets;

    /**
     * @ORM\ManyToMany(targetEntity=Tweet::class, mappedBy="retweets")
     */
    private $retweets;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="follow")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="followers")
     */
    private $follow;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
        $this->retweets = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->follow = new ArrayCollection();
    }

    public function __toString():string
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Tweet[]
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    public function addTweet(Tweet $tweet): self
    {
        if (!$this->tweets->contains($tweet)) {
            $this->tweets[] = $tweet;
            $tweet->setAuthor($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): self
    {
        if ($this->tweets->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getAuthor() === $this) {
                $tweet->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tweet[]
     */
    public function getRetweets(): Collection
    {
        return $this->retweets;
    }

    public function addRetweet(Tweet $retweet): self
    {
        if (!$this->retweets->contains($retweet)) {
            $this->retweets[] = $retweet;
            $retweet->addRetweet($this);
        }

        return $this;
    }

    public function removeRetweet(Tweet $retweet): self
    {
        if ($this->retweets->removeElement($retweet)) {
            $retweet->removeRetweet($this);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
        }

        return $this;
    }

    public function removeFollower(self $follower): self
    {
        $this->followers->removeElement($follower);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollow(): Collection
    {
        return $this->follow;
    }

    public function addFollow(self $follow): self
    {
        if (!$this->follow->contains($follow)) {
            $this->follow[] = $follow;
            $follow->addFollower($this);
        }

        return $this;
    }

    public function removeFollow(self $follow): self
    {
        if ($this->follow->removeElement($follow)) {
            $follow->removeFollower($this);
        }

        return $this;
    }
}
