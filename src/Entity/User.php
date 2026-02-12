<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: "Le nom d'utilisateur doit être unique")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 256)]
    #[Assert\NotBlank(message: "Veuillez encoder un mot de passe")]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[@$!%*#?&])[A-Za-z@$!%*#?&]{8}$/",
        message: "Le password doit contenir au moins 1 majuscule et 1 caractère spécial"
    )]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: "La longueur doit être de 8 caractères"
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: "L'email doit être valide")]
    private ?string $mail = null;


    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Role $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }
    private $confirm_password;

    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword($confirm_password): self
    {
        $this->confirm_password = $confirm_password;
        return $this;
    }

}
