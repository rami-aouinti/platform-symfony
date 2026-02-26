<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260624110000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Create media table with ownership, metadata and lifecycle fields.';
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

        $this->addSql('CREATE TABLE media (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", owner_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(255) NOT NULL, path VARCHAR(1024) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT NOT NULL, status VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", UNIQUE INDEX uq_media_owner_path (owner_id, path), INDEX idx_media_owner_id (owner_id), INDEX idx_media_status (status), INDEX idx_media_mime_type (mime_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_MEDIA_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_MEDIA_OWNER');
        $this->addSql('DROP TABLE media');
    }
}
