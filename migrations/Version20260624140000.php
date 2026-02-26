<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create resume_experience, resume_education and resume_skill tables.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \"mysql\".');

        $this->addSql('CREATE TABLE resume_education (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", school_name VARCHAR(255) NOT NULL, degree VARCHAR(255) NOT NULL, field_of_study VARCHAR(255) DEFAULT NULL, level VARCHAR(32) NOT NULL, start_date DATE NOT NULL COMMENT "(DC2Type:date_immutable)", end_date DATE DEFAULT NULL COMMENT "(DC2Type:date_immutable)", is_current TINYINT(1) NOT NULL DEFAULT 0, description LONGTEXT DEFAULT NULL, sort_order INT NOT NULL DEFAULT 0, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_resume_education_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resume_experience (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", title VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, employment_type VARCHAR(32) NOT NULL, start_date DATE NOT NULL COMMENT "(DC2Type:date_immutable)", end_date DATE DEFAULT NULL COMMENT "(DC2Type:date_immutable)", is_current TINYINT(1) NOT NULL DEFAULT 0, location VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, sort_order INT NOT NULL DEFAULT 0, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_resume_experience_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resume_skill (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", resume_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(128) NOT NULL, level VARCHAR(32) NOT NULL, years_experience SMALLINT DEFAULT NULL, sort_order INT NOT NULL DEFAULT 0, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_resume_skill_resume_sort (resume_id, sort_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume_education ADD CONSTRAINT FK_RESUME_EDUCATION_RESUME FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resume_experience ADD CONSTRAINT FK_RESUME_EXPERIENCE_RESUME FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resume_skill ADD CONSTRAINT FK_RESUME_SKILL_RESUME FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \"mysql\".');

        $this->addSql('ALTER TABLE resume_education DROP FOREIGN KEY FK_RESUME_EDUCATION_RESUME');
        $this->addSql('ALTER TABLE resume_experience DROP FOREIGN KEY FK_RESUME_EXPERIENCE_RESUME');
        $this->addSql('ALTER TABLE resume_skill DROP FOREIGN KEY FK_RESUME_SKILL_RESUME');
        $this->addSql('DROP TABLE resume_education');
        $this->addSql('DROP TABLE resume_experience');
        $this->addSql('DROP TABLE resume_skill');
    }
}
