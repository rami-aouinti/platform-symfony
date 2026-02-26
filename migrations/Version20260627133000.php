<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260627133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create project table and link tasks to projects.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", owner_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_project_owner_id (owner_id), INDEX idx_project_status (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE task ADD project_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_task_project_id ON task (project_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('DROP INDEX idx_task_project_id ON task');
        $this->addSql('ALTER TABLE task DROP project_id');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7E3C61F9');
        $this->addSql('DROP TABLE project');
    }
}
