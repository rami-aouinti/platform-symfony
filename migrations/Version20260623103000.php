<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623103000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add job_application table with candidate and decision workflow support.';
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

        $this->addSql('CREATE TABLE job_application (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', job_offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', candidate_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', cover_letter LONGTEXT DEFAULT NULL, cv_url VARCHAR(2048) DEFAULT NULL, attachments JSON DEFAULT NULL, status VARCHAR(32) NOT NULL COMMENT \'(DC2Type:string)\', decided_by_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', decided_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_job_application_job_offer_status (job_offer_id, status), INDEX idx_job_application_candidate_status (candidate_id, status), INDEX IDX_JOB_APPLICATION_DECIDED_BY (decided_by_id), UNIQUE INDEX uq_job_application_job_offer_candidate (job_offer_id, candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_JOB_OFFER FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_CANDIDATE FOREIGN KEY (candidate_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_DECIDED_BY FOREIGN KEY (decided_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_JOB_OFFER');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_CANDIDATE');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_DECIDED_BY');
        $this->addSql('DROP INDEX idx_job_application_job_offer_status ON job_application');
        $this->addSql('DROP INDEX idx_job_application_candidate_status ON job_application');
        $this->addSql('DROP INDEX IDX_JOB_APPLICATION_DECIDED_BY ON job_application');
        $this->addSql('DROP INDEX uq_job_application_job_offer_candidate ON job_application');
        $this->addSql('DROP TABLE job_application');
    }
}
