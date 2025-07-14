<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250713141219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename notation column to rating in owner table';
    }

    public function up(Schema $schema): void
    {
        // Rename notation column to rating in owner table
        $this->addSql('ALTER TABLE owner CHANGE notation rating DOUBLE PRECISION DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Rename rating column back to notation in owner table
        $this->addSql('ALTER TABLE owner CHANGE rating notation DOUBLE PRECISION DEFAULT 0 NOT NULL');
    }
}
