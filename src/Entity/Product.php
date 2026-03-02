<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(length: 100)]
    private ?string $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $extra = null;

    /**
     * @var Collection<int, ReservationItem>
     */
    #[ORM\OneToMany(targetEntity: ReservationItem::class, mappedBy: 'product')]
    private Collection $reservationItems;

    public function __construct()
    {
        $this->reservationItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    public function setExtra(?string $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return Collection<int, ReservationItem>
     */
    public function getReservationItems(): Collection
    {
        return $this->reservationItems;
    }

    public function addReservationItem(ReservationItem $reservationItem): static
    {
        if (!$this->reservationItems->contains($reservationItem)) {
            $this->reservationItems->add($reservationItem);
            $reservationItem->setProduct($this);
        }

        return $this;
    }

    public function removeReservationItem(ReservationItem $reservationItem): static
    {
        if ($this->reservationItems->removeElement($reservationItem)) {
            // set the owning side to null (unless already changed)
            if ($reservationItem->getProduct() === $this) {
                $reservationItem->setProduct(null);
            }
        }

        return $this;
    }
}