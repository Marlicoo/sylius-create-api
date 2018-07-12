<?php

namespace App\UseCase\Query\Ecommerce;


class WebsiteView implements QueryViewInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $userPassword;

    /** @var string */
    private $userLogin;

    /**
     * WebsiteView constructor.
     * @param string $url
     * @param string $userPassword
     * @param string $userLogin
     */
    public function __construct($url, $userPassword, $userLogin)
    {
        $this->url = $url;
        $this->userPassword = $userPassword;
        $this->userLogin = $userLogin;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getUserPassword(): string
    {
        return $this->userPassword;
    }

    /**
     * @return string
     */
    public function getUserLogin(): string
    {
        return $this->userLogin;
    }

}
