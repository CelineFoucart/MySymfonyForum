<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        minMessage: 'Votre message doit faire au moins {{ limit }} caractères'
    )]
    private $message;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reports')]
    private $author;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'reports')]
    private $post;

    #[ORM\Column(type: 'string', length: 4, options:['defaults' => 'post'])]
    private $type;

    #[ORM\ManyToOne(targetEntity: PrivateMessage::class, inversedBy: 'reports')]
    private $privateMessage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrivateMessage(): ?PrivateMessage
    {
        return $this->privateMessage;
    }

    public function setPrivateMessage(?PrivateMessage $privateMessage): self
    {
        $this->privateMessage = $privateMessage;

        return $this;
    }
}
