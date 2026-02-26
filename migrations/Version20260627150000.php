<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260627150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create task_request table for status change requests.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE task_request (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", task_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", requester_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", reviewer_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", type VARCHAR(64) NOT NULL, requested_status VARCHAR(64) DEFAULT NULL, status VARCHAR(64) NOT NULL, note LONGTEXT DEFAULT NULL, resolved_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_task_request_task_id (task_id), INDEX idx_task_request_requester_id (requester_id), INDEX idx_task_request_reviewer_id (reviewer_id), INDEX idx_task_request_status (status), INDEX idx_task_request_type (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_request ADD CONSTRAINT FK_BB1A30418DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_request ADD CONSTRAINT FK_BB1A3041C4663E4 FOREIGN KEY (requester_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_request ADD CONSTRAINT FK_BB1A3041A76ED395 FOREIGN KEY (reviewer_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_request DROP FOREIGN KEY FK_BB1A30418DB60186');
        $this->addSql('ALTER TABLE task_request DROP FOREIGN KEY FK_BB1A3041C4663E4');
        $this->addSql('ALTER TABLE task_request DROP FOREIGN KEY FK_BB1A3041A76ED395');
        $this->addSql('DROP TABLE task_request');
    }
}
