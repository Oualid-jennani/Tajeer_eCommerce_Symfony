<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Table;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Table(name="User")
 * @InheritanceType("SINGLE_TABLE")
 * @UniqueEntity( fields={"username","email"},message="Username or email already exist.")
 * @DiscriminatorColumn(name="user_role", type="string")
 * @DiscriminatorMap( {
 *     "user" = "User" ,
 *     "seller" = "Seller",
 *     "admin" = "Admin",
 *     "customer" = "Customer",
 *      } )
 * @method string getUserIdentifier()
 */
class User implements UserInterface, Serializable
{

    public function serialize()
    {
        // TODO: Implement serialize() method.
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
        list($this->id, $this->username, $this->password,) = unserialize($serialized);
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column( name="username" ,type="string", length=255,unique=true)
     * @Assert\NotBlank()
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $fullName;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private string $phoneNumber;

    /**
     * @ORM\Column(name="email",type="string", length=255,unique=true)
     * @Assert\NotBlank()
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password;

    /**
     * @Assert\EqualTo(
     *     propertyPath="password",
     *     message="Vous n'avez pas tapé le meme mot de passe"
     * )
     */
    public ?string $confirm_password;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="users")
     */
    private ?City $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $emailVerify;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     *
     * @ORM\Column(type="array")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 800,
     *     minHeight = 200,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 200px",
     *     minWidthMessage="Please provide an image with min  width 200px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     * )
     */
    private $brochureProfile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEmailVerify(): ?bool
    {
        return $this->emailVerify;
    }

    public function setEmailVerify(?bool $emailVerify): self
    {
        $this->emailVerify = $emailVerify;

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

    public function getRoles():array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public  function  setRoles ( array  $roles )
    {
        $this->roles  =  $roles ;

        // permet le chaînage
        return  $this ;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrochureProfile()
    {
        return $this->brochureProfile;
    }

    /**
     * @param mixed $brochureProfile
     */
    public function setBrochureProfile($brochureProfile): void
    {
        $this->brochureProfile = $brochureProfile;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

}
