<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260628120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace resume JSON fields with relational resume items and add references, projects and languages.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE resume DROP experiences, DROP education, DROP skills, DROP links');

        $this->addSql('CREATE TABLE resume_reference (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(255) NOT NULL, relation_name VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(64) DEFAULT NULL, sort_order INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_resume_reference_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume_reference ADD CONSTRAINT FK_3F90B06D56D32A0B FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE resume_project (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, project_url VARCHAR(2048) DEFAULT NULL, repository_url VARCHAR(2048) DEFAULT NULL, start_date DATE DEFAULT NULL COMMENT "(DC2Type:date_immutable)", end_date DATE DEFAULT NULL COMMENT "(DC2Type:date_immutable)", sort_order INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_resume_project_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume_project ADD CONSTRAINT FK_2924E6BD56D32A0B FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE resume_language (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(128) NOT NULL, level VARCHAR(64) DEFAULT NULL, sort_order INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_resume_language_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume_language ADD CONSTRAINT FK_C63B32D756D32A0B FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE resume_reference DROP FOREIGN KEY FK_3F90B06D56D32A0B');
        $this->addSql('ALTER TABLE resume_project DROP FOREIGN KEY FK_2924E6BD56D32A0B');
        $this->addSql('ALTER TABLE resume_language DROP FOREIGN KEY FK_C63B32D756D32A0B');
        $this->addSql('DROP TABLE resume_reference');
        $this->addSql('DROP TABLE resume_project');
        $this->addSql('DROP TABLE resume_language');

        $this->addSql('ALTER TABLE resume ADD experiences JSON NOT NULL, ADD education JSON NOT NULL, ADD skills JSON NOT NULL, ADD links JSON NOT NULL');
    }
}
