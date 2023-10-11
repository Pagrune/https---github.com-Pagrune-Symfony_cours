<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?User $idlikeuser = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Comment $idlikecomments = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getIdlikeuser(): ?User
    {
        return $this->idlikeuser;
    }

    public function setIdlikeuser(?User $idlikeuser): static
    {
        $this->idlikeuser = $idlikeuser;

        return $this;
    }

    public function getIdlikecomments(): ?Comment
    {
        return $this->idlikecomments;
    }

    public function setIdlikecomments(?Comment $idlikecomments): static
    {
        $this->idlikecomments = $idlikecomments;

        return $this;
    }
}
