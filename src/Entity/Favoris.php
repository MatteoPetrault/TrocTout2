<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    private ?Annonce $annnonce = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_ajout_favoris = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAnnnonce(): ?Annonce
    {
        return $this->annnonce;
    }

    public function setAnnnonce(?Annonce $annnonce): static
    {
        $this->annnonce = $annnonce;

        return $this;
    }

    public function getDateAjoutFavoris(): ?\DateTimeInterface
    {
        return $this->date_ajout_favoris;
    }

    public function setDateAjoutFavoris(\DateTimeInterface $date_ajout_favoris): static
    {
        $this->date_ajout_favoris = $date_ajout_favoris;

        return $this;
    }
}
