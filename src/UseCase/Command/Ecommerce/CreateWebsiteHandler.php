<?php

namespace App\UseCase\Command\Ecommerce;


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
        $syliusConfig = $this->containerDataFactory->prepareData(DockerApi::IMAGE_ECOMMERCE, $command->getCompanyName());

        $this->docker->createContainer($databaseConfig);
        $this->docker->createContainer($syliusConfig, $command->getLogoUrl());

        sleep(20);

        $passwordFactory = new Factory();
        $password = $passwordFactory->getMediumStrengthGenerator()->generateString(10);

        $this->sylius->createAdmin($syliusConfig->getContainerName(), $command->getEmail(), $password, $command->getEmail());

        $this->docker->cmd(["sh", "-c", "cd sylius/ && php bin/console doctrine:query:sql 'insert into sylius_gateway_config values (3, \"rbpl\", \"rbpl\", \"{}\")' \
                       && php bin/console doctrine:query:sql 'insert into sylius_payment_method values(3, 3, \"rbpl\", NULL, 1, 2, \"2018-06-21 11:38:06\", \"2018-06-21 11:38:06\")' \
                       && php bin/console doctrine:query:sql 'insert into sylius_payment_method_channels values (3, 2)' \
                       && php bin/console doctrine:query:sql 'insert into sylius_payment_method_translation values(3, 3, \"Pay With R-Pay\", NULL, NULL, \"en_US\" )'"], $syliusConfig->getContainerName());

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
        $this->em->getConnection()->beginTransaction();
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
            $ecommerce->setUrl(sprintf('%s%s.localhost:%s','http://', $command->getCompanyName(), $syliusConfig->getPort()));

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
