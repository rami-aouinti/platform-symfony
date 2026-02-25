<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623120001 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Align job_offer filter columns: remote_mode naming and dedicated indexes for status and experience_level.';
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

        $this->addSql('ALTER TABLE job_offer DROP INDEX idx_job_offer_remote_policy, CHANGE remote_policy remote_mode VARCHAR(32) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_job_offer_remote_mode ON job_offer (remote_mode)');
        $this->addSql('CREATE INDEX idx_job_offer_status ON job_offer (status)');
        $this->addSql('CREATE INDEX idx_job_offer_experience_level ON job_offer (experience_level)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP INDEX idx_job_offer_experience_level ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_status ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_remote_mode ON job_offer');
        $this->addSql('ALTER TABLE job_offer CHANGE remote_mode remote_policy VARCHAR(32) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_job_offer_remote_policy ON job_offer (remote_policy)');
    }
}
