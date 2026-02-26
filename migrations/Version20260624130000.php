<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260624130000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add nullable resume relation on job_application for internal CV support.';
    }

    #[Override]
    public function isTransactional(): bool
    {
        return false;
    }

    #[Override]
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_application ADD resume_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_RESUME FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_job_application_resume ON job_application (resume_id)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_RESUME');
        $this->addSql('DROP INDEX idx_job_application_resume ON job_application');
        $this->addSql('ALTER TABLE job_application DROP resume_id');
    }
}
