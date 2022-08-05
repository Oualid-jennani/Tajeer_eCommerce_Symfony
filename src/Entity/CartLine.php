<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CartLineRepository::class)
 */
class CartLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull(message="Please provide a quantity")
     * @Assert\NotBlank(message="This value should not be blank")
     * @Assert\Positive(message="Quantity has an invalid value")
     * @Assert\Regex(pattern="/^(\d+)$/", message="The quantity should be a valid numeric")
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Variant::class, inversedBy="cartLines",fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $variant;

    /**
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartLines",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="cartLines",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    public function setVariant(?Variant $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function total()
    {
        $pr = 0.0;
        if($this->variant != null) {
            $pr = $this->getVariant()->getPrice();
        }
        else {
            $pr = $this->getProduct()->getPrice();
        }

        return $pr * $this->getQuantity();
    }
}