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
        return 'Add offer table with company and creator relations.';
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

        $this->addSql('CREATE TABLE offer (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', company_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', created_by_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_offer_company_status (company_id, status), INDEX IDX_OFFER_CREATED_BY (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_OFFER_COMPANY FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_OFFER_CREATED_BY FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_OFFER_COMPANY');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_OFFER_CREATED_BY');
        $this->addSql('DROP TABLE offer');
    }
}
