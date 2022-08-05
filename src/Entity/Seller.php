<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SellerRepository::class)
 */
class Seller extends User
{
//    /**
//     * @ORM\Id
//     * @ORM\GeneratedValue
//     * @ORM\Column(type="integer")
//     */
//    private ?int $id;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $birthDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $storeName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $storePhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $emergencyPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $coverImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $storeImage;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $About;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $localisation;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="seller")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="seller")
     */
    private $carts;

    /**
     * @ORM\OneToMany(targetEntity=SliderSeller::class, mappedBy="seller")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $sliderSellers;

    public function __construct()
    {
        $this->setRoles(['ROLE_SELLER']);
        $this->products = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
        $this->carts = new ArrayCollection();
        $this->sliderSellers = new ArrayCollection();
    }

//    public function getId(): ?int
//    {
//        return $this->id;
//    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function setStoreName(string $storeName): self
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function getStorePhone(): ?string
    {
        return $this->storePhone;
    }

    public function setStorePhone(?string $storePhone): self
    {
        $this->storePhone = $storePhone;

        return $this;
    }

    public function getEmergencyPhone(): ?string
    {
        return $this->emergencyPhone;
    }

    public function setEmergencyPhone(?string $emergencyPhone): self
    {
        $this->emergencyPhone = $emergencyPhone;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getStoreImage(): ?string
    {
        return $this->storeImage;
    }

    public function setStoreImage(?string $storeImage): self
    {
        $this->storeImage = $storeImage;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSeller($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSeller() === $this) {
                $product->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setSeller($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getSeller() === $this) {
                $cart->setSeller(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->About;
    }

    public function setAbout(?string $About): self
    {
        $this->About = $About;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    /**
     * @return Collection|SliderSeller[]
     */
    public function getSliderSellers(): Collection
    {
        return $this->sliderSellers;
    }

    public function addSliderSeller(SliderSeller $sliderSeller): self
    {
        if (!$this->sliderSellers->contains($sliderSeller)) {
            $this->sliderSellers[] = $sliderSeller;
            $sliderSeller->setSeller($this);
        }

        return $this;
    }

    public function removeSliderSeller(SliderSeller $sliderSeller): self
    {
        if ($this->sliderSellers->removeElement($sliderSeller)) {
            // set the owning side to null (unless already changed)
            if ($sliderSeller->getSeller() === $this) {
                $sliderSeller->setSeller(null);
            }
        }

        return $this;
    }

    // custom function Yassine

    public function getProductsByCategoriesAndPricesRange($pricesRange,Category$category)
    {
        $firstPrice = explode(',',$pricesRange[0]);
        $lastPrice = explode(',',$pricesRange[count($pricesRange)-1]);

        return $this->getProducts()->filter(function (Product $product) use ($category,$lastPrice,$firstPrice) {
            if ($product->getCategory() === $category &&
                    $product->getPrice() >= $firstPrice[0] && $product->getPrice() <= $lastPrice[1]) {
                return $product;
            };
        });
    }

    public function getProductsByCategory(Category $category)
    {
        return $this->getProducts()->filter(function (Product $product) use ($category) {
            if ($product->getCategory() === $category) return $product;
        });
    }

    public function getProductsByPricesRange($pricesRange)
    {
        $firstPrice = explode(',',$pricesRange[0]);
        $lastPrice = explode(',',$pricesRange[count($pricesRange)-1]);

        return $this->getProducts()->filter(function (Product $product)  use ($lastPrice,$firstPrice) {
            if ($product->getPrice() >= $firstPrice[0] && $product->getPrice() <= $lastPrice[1])  return $product;
        });
    }



}
