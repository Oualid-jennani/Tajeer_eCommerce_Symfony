<?php

namespace App\Entity;

use App\Repository\SliderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=SliderRepository::class)
 */
class Slider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 1095,
     *     minHeight = 400,
     *     maxHeight = 562,
     *     maxHeightMessage="Please provide an image with max height 400px",
     *     maxWidthMessage="Please provide an image with max  width 1095px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     * )
     */
    private $brochure;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageUrl;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrochure()
    {
        return $this->brochure;
    }

    /**
     * @param mixed $brochure
     */
    public function setBrochure($brochure): void
    {
        $this->brochure = $brochure;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

}
