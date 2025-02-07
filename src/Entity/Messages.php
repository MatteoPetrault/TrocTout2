<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?User $envoyer = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?User $recevoir = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Annonce $annonces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenant(): ?string
    {
        return $this->contenant;
    }

    public function setContenant(string $contenant): static
    {
        $this->contenant = $contenant;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getEnvoyer(): ?User
    {
        return $this->envoyer;
    }

    public function setEnvoyer(?User $envoyer): static
    {
        $this->envoyer = $envoyer;

        return $this;
    }

    public function getRecevoir(): ?User
    {
        return $this->recevoir;
    }

    public function setRecevoir(?User $recevoir): static
    {
        $this->recevoir = $recevoir;

        return $this;
    }

    public function getAnnonces(): ?Annonce
    {
        return $this->annonces;
    }

    public function setAnnonces(?Annonce $annonces): static
    {
        $this->annonces = $annonces;

        return $this;
    }
}
