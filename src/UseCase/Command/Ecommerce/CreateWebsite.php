<?php

namespace App\UseCase\Command\Ecommerce;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWebsite
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="No company name provided")
     */
    private $companyName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="No email provided")
     * @Assert\Email(message="Invalid email address provided")
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="No logo url provided")
     * @Assert\Url()
     */
    private $logoUrl;

    /**
     * @var string
     */
    private $companySubtitle;

    /**
     * CreateWebsite constructor.
     * @param string $companyName
     * @param string $email
     * @param string $logoUrl
     */
    public function __construct($companyName, $email, $logoUrl, $companySubtitle)
    {
        $this->companyName = $companyName;
        $this->email = $email;
        $this->logoUrl = $logoUrl;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    /**
     * @return string
     */
    public function getCompanySubtitle(): string
    {
        return $this->companySubtitle;
    }

}

