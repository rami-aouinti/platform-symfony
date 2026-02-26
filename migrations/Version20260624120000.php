<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260624120000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Create resume table with owner, structured data fields and lifecycle timestamps.';
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

        $this->addSql('CREATE TABLE resume (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", owner_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", title VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, experiences JSON NOT NULL, education JSON NOT NULL, skills JSON NOT NULL, links JSON NOT NULL, is_public TINYINT(1) NOT NULL DEFAULT 0, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_resume_owner_created_at (owner_id, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume ADD CONSTRAINT FK_RESUME_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE resume DROP FOREIGN KEY FK_RESUME_OWNER');
        $this->addSql('DROP TABLE resume');
    }
}
