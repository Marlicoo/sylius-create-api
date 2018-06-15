<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="EcommerceWebsite", mappedBy="user")
     */
    private $ecommerceShop;

    public function getId()
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEcommerceShop(): ?EcommerceWebsite
    {
        return $this->ecommerceShop;
    }

    public function setEcommerceShop(?EcommerceWebsite $ecommerceShop): self
    {
        $this->ecommerceShop = $ecommerceShop;

        $newUser = $ecommerceShop === null ? null : $this;
        if ($newUser !== $ecommerceShop->getUser()) {
            $ecommerceShop->setUser($newUser);
        }

        return $this;
    }
}
