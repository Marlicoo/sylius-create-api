<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EcommerceWebsiteRepository")
 */
class EcommerceWebsite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="ecommerceShop")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Container", mappedBy="ecommerceWebsite", cascade={"persist"})
     */
    private $containers;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", length=100)
     */
    private $enabled;

    public function __construct()
    {
        $this->containers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|Container[]
     */
    public function getContainers(): Collection
    {
        return $this->containers;
    }

    public function addContainer(Container $container): self
    {
        if (!$this->containers->contains($container)) {
            $this->containers[] = $container;
            $container->setEcommerceWebsite($this);
        }

        return $this;
    }

    public function removeContainer(Container $container): self
    {
        if ($this->containers->contains($container)) {
            $this->containers->removeElement($container);
            // set the owning side to null (unless already changed)
            if ($container->getEcommerceWebsite() === $this) {
                $container->setEcommerceWebsite(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
