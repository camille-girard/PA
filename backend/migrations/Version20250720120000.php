<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add isTwoFactorEnabled field to user table for 2FA status.
 */
final class Version20250720120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add isTwoFactorEnabled field to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD is_two_factor_enabled TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP is_two_factor_enabled');
    }
}
