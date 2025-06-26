<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608202714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix grammar in accommodation advantage field name';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation CHANGE adventage advantage LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation CHANGE advantage adventage LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
    }
}
