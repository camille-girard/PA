<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421122739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation ADD CONSTRAINT FK_2D3854127E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation ADD CONSTRAINT FK_2D38541259027487 FOREIGN KEY (theme_id) REFERENCES theme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images ADD CONSTRAINT FK_BD7B1A4B8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526C8F3692CD FOREIGN KEY (accommodation_id) REFERENCES accommodation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE owner ADD CONSTRAINT FK_CF60E67CBF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE support_ticket ADD CONSTRAINT FK_1F5A4D53A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD phone VARCHAR(255) DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD discr VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE8F3692CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP phone, DROP avatar, DROP is_verified, DROP created_at, DROP discr
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation DROP FOREIGN KEY FK_2D3854127E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation DROP FOREIGN KEY FK_2D38541259027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526C8F3692CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE support_ticket DROP FOREIGN KEY FK_1F5A4D53A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67CBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP FOREIGN KEY FK_C7440455BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accommodation_images DROP FOREIGN KEY FK_BD7B1A4B8F3692CD
        SQL);
    }
}
