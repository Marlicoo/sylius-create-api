<?php

namespace App\Dto;


class ContainerConfig
{
    /** @var string */
    private $imageType;

    /** @var string */
    private $containerName;

    /** @var string */
    private $port;

    private $hostname;

    private $env;

    /**
     * ContainerConfig constructor.
     * @param string $imageType
     * @param string $containerName
     * @param string $port
     */
    public function __construct($imageType, $containerName, $port, $hostname, $env)
    {
        $this->imageType = $imageType;
        $this->containerName = $containerName;
        $this->port = $port;
        $this->hostname = $hostname;
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getImageType(): string
    {
        return $this->imageType;
    }

    /**
     * @return string
     */
    public function getContainerName(): string
    {
        return $this->containerName;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

}
