<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250621122803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images DROP FOREIGN KEY FK_BD7B1A4B8F3692CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images CHANGE accommodation_id accommodation_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images ADD CONSTRAINT FK_BD7B1A4B8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images DROP FOREIGN KEY FK_BD7B1A4B8F3692CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images CHANGE accommodation_id accommodation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images ADD CONSTRAINT FK_BD7B1A4B8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
