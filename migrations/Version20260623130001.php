<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623130001 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add job categories reference table and link job offers to taxonomy UUID filters.';
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

        $this->addSql('CREATE TABLE job_category (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", code VARCHAR(64) NOT NULL, name VARCHAR(128) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", UNIQUE INDEX uq_job_category_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_offer ADD job_category_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql('CREATE INDEX idx_job_offer_job_category_id ON job_offer (job_category_id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_EA5A54D688434E6B FOREIGN KEY (job_category_id) REFERENCES job_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_job_offer_skill_skill_offer ON job_offer_skill (skill_id, job_offer_id)');
        $this->addSql('CREATE INDEX idx_job_offer_language_language_offer ON job_offer_language (language_id, job_offer_id)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP INDEX idx_job_offer_language_language_offer ON job_offer_language');
        $this->addSql('DROP INDEX idx_job_offer_skill_skill_offer ON job_offer_skill');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_EA5A54D688434E6B');
        $this->addSql('DROP INDEX idx_job_offer_job_category_id ON job_offer');
        $this->addSql('ALTER TABLE job_offer DROP job_category_id');
        $this->addSql('DROP TABLE job_category');
    }
}
