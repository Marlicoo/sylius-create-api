<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180615110754 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE container (id INT AUTO_INCREMENT NOT NULL, ecommerce_website_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, port VARCHAR(8) NOT NULL, type INT NOT NULL, INDEX IDX_C7A2EC1BEA2DCCFE (ecommerce_website_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecommerce_website (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, url VARCHAR(100) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_56FD5827A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE container ADD CONSTRAINT FK_C7A2EC1BEA2DCCFE FOREIGN KEY (ecommerce_website_id) REFERENCES ecommerce_website (id)');
        $this->addSql('ALTER TABLE ecommerce_website ADD CONSTRAINT FK_56FD5827A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ecommerce_website DROP FOREIGN KEY FK_56FD5827A76ED395');
        $this->addSql('ALTER TABLE container DROP FOREIGN KEY FK_C7A2EC1BEA2DCCFE');
        $this->addSql('DROP TABLE container');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE ecommerce_website');
    }
}
