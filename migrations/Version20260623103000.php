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
        return 'Add job_application table and status workflow support.';
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

        $this->addSql('CREATE TABLE job_application (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', status VARCHAR(32) NOT NULL COMMENT \'(DC2Type:string)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_job_application_offer_status (offer_id, status), INDEX idx_job_application_user_status (user_id, status), UNIQUE INDEX uq_job_application_user_offer (user_id, offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_OFFER FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_JOB_APPLICATION_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_OFFER');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_JOB_APPLICATION_USER');
        $this->addSql('DROP TABLE job_application');
    }
}
