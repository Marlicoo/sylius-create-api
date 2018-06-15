<?php

namespace App\UseCase\Ecommerce;


use App\Dto\ContainerConfig;
use App\Entity\Container;
use App\Entity\EcommerceWebsite;
use App\Entity\User;
use App\Service\ContainerDataFactory;
use App\Service\DockerApi;
use App\Service\SyliusApi;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RandomLib\Factory;

class CreateWebsiteHandler
{
    const ECOMMERCE_NAME = 'rbpl-sylius-%s';
    const DATABASE_NAME = 'rbpl-mysql-%s';

    /** @var DockerApi */
    private $docker;

    /** @var  SyliusApi */
    private $sylius;

    /**
     * @var ContainerDataFactory
     */
    private $containerDataFactory;

    private $em;


    /**
     * CreateWebsiteHandler constructor.
     * @param DockerApi $docker
     * @param SyliusApi $sylius
     */
    public function __construct(DockerApi $docker, SyliusApi $sylius, ContainerDataFactory $containerDataFactory, EntityManagerInterface $em)
    {
        $this->docker = $docker;
        $this->sylius = $sylius;
        $this->containerDataFactory = $containerDataFactory;
        $this->em = $em;
    }

    public function handle(CreateWebsite $command)
    {
        $databaseConfig = $this->containerDataFactory->prepareData(DockerApi::IMAGE_DATABASE, $command->getCompanyName());
        $syliusConfig   = $this->containerDataFactory->prepareData(DockerApi::IMAGE_ECOMMERCE, $command->getCompanyName());

        $this->docker->createContainer($databaseConfig);
        $this->docker->createContainer($syliusConfig, $command->getLogoUrl());

        sleep(15);

        $passwordFactory = new Factory();
        $password = $passwordFactory->getMediumStrengthGenerator()->generateString(10);

        $this->sylius->createAdmin($syliusConfig->getContainerName(), $command->getEmail(), $password, $command->getEmail() );

        $this->saveData($command, $syliusConfig, $databaseConfig, $password);

    }

    /**
     * @param CreateWebsite $command
     * @param $syliusConfig
     * @param $databaseConfig
     * @param $password
     * @throws Exception
     */
    public function saveData(CreateWebsite $command, ContainerConfig $syliusConfig, ContainerConfig $databaseConfig, $password): void
    {
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $syliusContainer = new Container();
            $syliusContainer->setName($syliusConfig->getContainerName())
                ->setPort($syliusConfig->getPort())
                ->setType(Container::TYPE_ECOMMERCE);

            $databaseContainer = new Container();
            $databaseContainer->setName($databaseConfig->getContainerName())
                ->setPort($databaseConfig->getPort())
                ->setType(Container::TYPE_DATABASE);

            $ecommerce = new EcommerceWebsite();
            $ecommerce->addContainer($syliusContainer);
            $ecommerce->addContainer($databaseContainer);
            $ecommerce->setEnabled(true);
            $ecommerce->setUrl(sprintf('%s.localhost:%s', $command->getCompanyName(), $syliusConfig->getPort()));

            $user = new User();
            $user->setEmail($command->getEmail())
                ->setPassword($password)
                ->setUsername($command->getEmail())
                ->setEcommerceShop($ecommerce);


            $this->em->persist($user);
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
}
