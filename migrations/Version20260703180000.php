<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add key_name and is_public to user_application';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE user_application ADD key_name VARCHAR(255) DEFAULT NULL, ADD is_public TINYINT(1) DEFAULT 0 NOT NULL");
        $this->addSql("UPDATE user_application SET key_name = CONCAT(LOWER(REPLACE(name, ' ', '-')), '-', LOWER(HEX(id))) WHERE key_name IS NULL");
        $this->addSql('CREATE UNIQUE INDEX uq_user_application_key_name ON user_application (key_name)');
        $this->addSql('ALTER TABLE user_application MODIFY key_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_application DROP INDEX uq_user_application_key_name');
        $this->addSql('ALTER TABLE user_application DROP key_name, DROP is_public');
    }
}
