<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250601092451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bio to owner and slug to theme';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE owner ADD bio LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme ADD slug VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE owner DROP bio
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE theme DROP slug
        SQL);
    }
}
