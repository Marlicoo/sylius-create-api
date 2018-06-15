<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContainerRepository")
 */
class Container
{
    const TYPE_ECOMMERCE = 1;
    const TYPE_DATABASE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100))
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $port;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="EcommerceWebsite", inversedBy="containers")
     */
    private $ecommerceWebsite;

    /**
     * @param integer $type
     * @throws \InvalidArgumentException
     */
    public function setType(int $type): void
    {
        if (!\in_array($type, [self::TYPE_DATABASE, self::TYPE_ECOMMERCE], true)) {
            throw new \InvalidArgumentException("Invalid container type");
        }
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getEcommerceWebsite(): ?EcommerceWebsite
    {
        return $this->ecommerceWebsite;
    }

    public function setEcommerceWebsite(?EcommerceWebsite $ecommerceWebsite): self
    {
        $this->ecommerceWebsite = $ecommerceWebsite;

        return $this;
    }
}
