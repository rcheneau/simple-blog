<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const R_ADMIN = 'ROLE_ADMIN';

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    /**
     * @param UuidV4             $id
     * @param string             $email
     * @param string             $username
     * @param array<int, string> $roles
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private UuidV4 $id,

        #[ORM\Column(length: 180, unique: true)]
        private string $email,

        #[ORM\Column(length: 20, unique: true)]
        private string $username,

        #[ORM\Column]
        private array  $roles = [],
    )
    {

    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string|null
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function updateEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param array<int, string> $roles
     *
     * @return $this
     */
    public function updateRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function updatePassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
