<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260701130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create quiz table for quiz module CRUD.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE quiz (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", owner_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) NOT NULL, difficulty VARCHAR(20) NOT NULL, time_limit INT DEFAULT NULL, is_published TINYINT(1) NOT NULL DEFAULT 0, starts_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", ends_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_quiz_owner_id (owner_id), INDEX idx_quiz_category (category), INDEX idx_quiz_difficulty (difficulty), INDEX idx_quiz_published (is_published), INDEX idx_quiz_starts_at (starts_at), INDEX idx_quiz_ends_at (ends_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92297E70AF FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92297E70AF');
        $this->addSql('DROP TABLE quiz');
    }
}
