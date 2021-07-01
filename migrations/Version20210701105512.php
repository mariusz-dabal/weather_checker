<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210701105512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE weather DROP FOREIGN KEY FK_4CD0D36E64D218E');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP INDEX IDX_4CD0D36E64D218E ON weather');
        $this->addSql('ALTER TABLE weather ADD city VARCHAR(255) NOT NULL, DROP location_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE weather ADD location_id INT DEFAULT NULL, DROP city');
        $this->addSql('ALTER TABLE weather ADD CONSTRAINT FK_4CD0D36E64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_4CD0D36E64D218E ON weather (location_id)');
    }
}
