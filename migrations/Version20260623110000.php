<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623110000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add listing and filtering metadata columns to job_offer with dedicated indexes.';
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

        $this->addSql('ALTER TABLE job_offer ADD salary_min INT DEFAULT NULL, ADD salary_max INT DEFAULT NULL, ADD salary_currency VARCHAR(3) DEFAULT NULL, ADD salary_period VARCHAR(32) DEFAULT NULL, ADD remote_policy VARCHAR(32) DEFAULT NULL, ADD experience_level VARCHAR(32) DEFAULT NULL, ADD work_time VARCHAR(32) DEFAULT NULL, ADD application_type VARCHAR(32) DEFAULT NULL, ADD published_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD city VARCHAR(128) DEFAULT NULL, ADD region VARCHAR(128) DEFAULT NULL, ADD country VARCHAR(2) DEFAULT NULL, ADD language_level VARCHAR(32) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_job_offer_published_at ON job_offer (published_at)');
        $this->addSql('CREATE INDEX idx_job_offer_work_time ON job_offer (work_time)');
        $this->addSql('CREATE INDEX idx_job_offer_employment_type ON job_offer (employment_type)');
        $this->addSql('CREATE INDEX idx_job_offer_remote_policy ON job_offer (remote_policy)');
        $this->addSql('CREATE INDEX idx_job_offer_city ON job_offer (city)');
        $this->addSql('CREATE INDEX idx_job_offer_region ON job_offer (region)');
        $this->addSql('CREATE INDEX idx_job_offer_salary_min ON job_offer (salary_min)');
        $this->addSql('CREATE INDEX idx_job_offer_salary_max ON job_offer (salary_max)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP INDEX idx_job_offer_published_at ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_work_time ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_employment_type ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_remote_policy ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_city ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_region ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_salary_min ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_salary_max ON job_offer');
        $this->addSql('ALTER TABLE job_offer DROP salary_min, DROP salary_max, DROP salary_currency, DROP salary_period, DROP remote_policy, DROP experience_level, DROP work_time, DROP application_type, DROP published_at, DROP city, DROP region, DROP country, DROP language_level');
    }
}
