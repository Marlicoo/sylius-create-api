<?php

namespace App\UseCase\Ecommerce;


use App\Entity\Container;
use App\Entity\EcommerceWebsite;
use App\Entity\User;
use App\Repository\ContainerRepository;
use App\Service\ContainerDataFactory;
use App\Service\DockerApi;
use App\Service\SyliusApi;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RandomLib\Factory;
use stdClass;

class CreateShopHandler
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
     * CreateShopHandler constructor.
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

    public function handle(CreateShop $command)
    {
        $databaseConfig = $this->containerDataFactory->prepareData(DockerApi::IMAGE_DATABASE);
        $syliusConfig = $this->containerDataFactory->prepareData(DockerApi::IMAGE_ECOMMERCE);

        $this->docker->createContainer($databaseConfig);
        $this->docker->createContainer($syliusConfig, $command->getLogoUrl());

        sleep(15);

        $passwordFactory = new Factory();
        $password = $passwordFactory->getMediumStrengthGenerator()->generateString(10);

        $this->sylius->createAdmin($syliusConfig->getContainerName(), $command->getEmail(), $password, $command->getEmail() );

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
