<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260627120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create task table for Task module.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE task (id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", owner_id BINARY(16) DEFAULT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, priority VARCHAR(64) NOT NULL, status VARCHAR(64) NOT NULL, due_date DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", completed_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", created_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", updated_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", INDEX idx_task_owner_id (owner_id), INDEX idx_task_status (status), INDEX idx_task_priority (priority), INDEX idx_task_due_date (due_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB257E3C61F9');
        $this->addSql('DROP TABLE task');
    }
}
