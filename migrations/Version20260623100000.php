<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623100000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add job_offer table with company and creator relations.';
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

        $this->addSql('CREATE TABLE job_offer (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', company_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', created_by_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, location VARCHAR(255) NOT NULL, employment_type VARCHAR(64) NOT NULL, status VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_job_offer_company_status_created_at (company_id, status, created_at), INDEX IDX_JOB_OFFER_CREATED_BY (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_JOB_OFFER_COMPANY FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_JOB_OFFER_CREATED_BY FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_JOB_OFFER_COMPANY');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_JOB_OFFER_CREATED_BY');
        $this->addSql('DROP INDEX idx_job_offer_company_status_created_at ON job_offer');
        $this->addSql('DROP INDEX IDX_JOB_OFFER_CREATED_BY ON job_offer');
        $this->addSql('DROP TABLE job_offer');
    }
}
