<?php

namespace App\Service;


use App\Dto\ContainerConfig;
use App\Repository\ContainerRepository;
use Psr\Log\InvalidArgumentException;

class ContainerDataFactory
{
    const NAME_ECOMMERCE = 'rbpl-sylius-%s';
    const NAME_DATABASE = 'rbpl-mysql-%s';
    private static $mySqlEnv = [
        'MYSQL_ROOT_PASSWORD=sylius',
        'MYSQL_DATABASE=sylius_dev',
        'MYSQL_USER=sylius',
        'MYSQL_PASSWORD=sylius',
    ];

    private static $syliusEnv = [
        'SYLIUS_DATABASE_USER=sylius',
        'SYLIUS_DATABASE_PASSWORD=sylius',
        'SYLIUS_APP_DEV_PERMITTED=1'
    ];

    /** @var  ContainerRepository */
    private $containerRepository;

    private $imageDatabase;

    private $imageEcommerce;

    /**
     * ContainerConfigFactory constructor.
     * @param ContainerRepository $containerRepository
     */
    public function __construct(ContainerRepository $containerRepository, $imageDatabase, $imageEcommerce)
    {
        $this->containerRepository = $containerRepository;
        $this->imageDatabase = $imageDatabase;
        $this->imageEcommerce = $imageEcommerce;
    }

    /**
     * @param string $imageType
     * @return ContainerConfig
     */
    public function prepareData(string $imageType, string $companyName){

        switch($imageType){
            case DockerApi::IMAGE_DATABASE:
                $port = $this->getPort();
                $name = sprintf(self::NAME_DATABASE, $port);
                return new ContainerConfig($this->imageDatabase, $name, $port, $name, self::$mySqlEnv);

                break;
            case DockerApi::IMAGE_ECOMMERCE:
                $port = $this->getPort();

                /** @var int $databasePort */
                $databasePort = (int)$port -1;
                $name = sprintf(self::NAME_ECOMMERCE, $port);

                self::$syliusEnv[] = 'SYLIUS_DATABASE_HOST=rbpl-mysql-'. $databasePort;
                self::$syliusEnv[] = sprintf('VIRTUAL_HOST=%s.localhost', $companyName);

                return new ContainerConfig($this->imageEcommerce, $name, $port, 'sylius', self::$syliusEnv);
                break;

            default:
                throw new InvalidArgumentException('Image does not exist');
        }

    }

    private function getPort(){

        $port = file_get_contents('/application/files/port.txt');

        file_put_contents('/application/files/port.txt', $port + 1);

        return $port;
    }


}