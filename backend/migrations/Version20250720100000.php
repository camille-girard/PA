<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add totpSecret field to user table for TOTP authentication.
 */
final class Version20250720100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add TOTP secret field to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD totp_secret VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP totp_secret');
    }
}
