<?php

namespace App\UseCase\Query\Ecommerce;

use Doctrine\DBAL\Connection;

class WebsiteQuery implements QueryInterface
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByUserEmail($email){

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('u.password', 'u.username', 's.url')
            ->from('user', 'u')
            ->leftJoin('u','ecommerce_website',"s","u.id=s.user_id")
            ->where('u.email = :email')
            ->setParameter('email', $email);

        $statment = $queryBuilder->execute();

        $websiteData = $statment->fetch();

        return new WebsiteView($websiteData['url'], $websiteData['password'], $websiteData['username']);
    }

}

