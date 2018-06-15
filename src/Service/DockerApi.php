<?php

namespace App\Service;

use App\Dto\ContainerConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Docker\Docker;
use stdClass;

class DockerApi
{
    public const IMAGE_ECOMMERCE = 'rbpl6:local';
    public const IMAGE_DATABASE = 'mysql:5.7';

    /**
     * @var TemplateCreator
     */
    private $templateCreator;

    /**
     * DockerApi constructor.
     * @param TemplateCreator $templateCreator
     */
    public function __construct(TemplateCreator $templateCreator)
    {
        $this->templateCreator = $templateCreator;
    }


    public function createContainer(ContainerConfig $config, $logoUrl = null)
    {
        $docker = Docker::create();

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage($config->getImageType());
        $containerConfig->setEnv($config->getEnv());
        $containerConfig->setHostname($config->getHostname());


        $portBinding = new PortBinding();
        $portBinding->setHostPort($config->getPort());
        $portBinding->setHostIp('0.0.0.0');

        $portMap = new \ArrayObject();
        $ports = $config->getImageType() === self::IMAGE_DATABASE ? new \ArrayObject(['3306/tcp' => new stdClass]) : new \ArrayObject(['80/tcp' => new stdClass]);
        $containerConfig->setExposedPorts($ports);

        foreach ($ports as $port => $value) {
            $portMap[$port] = [$portBinding];
        }

        $hostConfig = new HostConfig();
        $hostConfig->setPortBindings($portMap);
        $hostConfig->setNetworkMode('shop-local'); #local network

        if ($config->getImageType() === self::IMAGE_ECOMMERCE) {

            $headerTemplate = $this->templateCreator->overrideHeaderTemplate($config->getContainerName(), $logoUrl);
            $adminTemplate = $this->templateCreator->overrideAdminLoginTemplate($config->getContainerName(), $logoUrl);
            $hostConfig->setBinds([$headerTemplate . ':/var/www/sylius/app/Resources/SyliusShopBundle/views/_header.html.twig', $adminTemplate . ':/var/www/sylius/app/Resources/SyliusUiBundle/views/Security/_login.html.twig']);
        }

        $containerConfig->setHostConfig($hostConfig);

        $container = $docker->containerCreate($containerConfig, ['name' => $config->getContainerName()]);
        $docker->containerStart($container->getId());

        if ($config->getImageType() === self::IMAGE_DATABASE) {
            sleep(15);
        } else {
            $this->cmd(["sh", "-c", "cd sylius/ &&  php bin/console sylius:install -n &&  php bin/console sylius:fixtures:load"], $config->getContainerName());
        }

        return $container;

    }

    public function cmd($cmd, $name)
    {
        $docker = Docker::create();
        $execConfig = new ContainersIdExecPostBody();
        $execConfig->setCmd($cmd);
        $execConfig->setUser('www-data');
        $execConfig->setAttachStderr(true);
        $execConfig->setAttachStdin(true);
        $execConfig->setAttachStdout(true);
        $execConfig->setTty(false);
        $execConfig->setPrivileged(true);

        $execManager = $docker->containerExec($name, $execConfig);

        $body = new ExecIdStartPostBody();
        $body->setDetach(false);
        $docker->execStart($execManager->getId(), $body);

        return true;
    }
}