<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260627170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update task_request to keep only requested status with time and add sprint entity.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sprint (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", start_date DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", end_date DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_sprint_start_date (start_date), INDEX idx_sprint_end_date (end_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_request ADD sprint_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", ADD time DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", DROP status, DROP resolved_at');
        $this->addSql('CREATE INDEX idx_task_request_sprint_id ON task_request (sprint_id)');
        $this->addSql('ALTER TABLE task_request ADD CONSTRAINT FK_TASK_REQUEST_SPRINT FOREIGN KEY (sprint_id) REFERENCES sprint (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX idx_task_request_status ON task_request');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_request DROP FOREIGN KEY FK_TASK_REQUEST_SPRINT');
        $this->addSql('DROP TABLE sprint');
        $this->addSql('DROP INDEX idx_task_request_sprint_id ON task_request');
        $this->addSql('ALTER TABLE task_request ADD status VARCHAR(64) NOT NULL COMMENT "(DC2Type:string)", ADD resolved_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", DROP sprint_id, DROP time');
        $this->addSql('CREATE INDEX idx_task_request_status ON task_request (status)');
    }
}
