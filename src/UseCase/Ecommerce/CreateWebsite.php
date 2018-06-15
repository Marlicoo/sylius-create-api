<?php

namespace App\UseCase\Ecommerce;


class CreateWebsite
{
    /** @var string */
    private $companyName;

    /** @var string */
    private $email;

    /** @var string */
    private $logoUrl;

    /** @var string */
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

