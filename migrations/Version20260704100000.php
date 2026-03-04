<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260704100000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Create media_folder table and link media to folders.';
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

        $this->addSql('CREATE TABLE media_folder (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", owner_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", parent_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_media_folder_owner_id (owner_id), INDEX idx_media_folder_parent_id (parent_id), UNIQUE INDEX uq_media_folder_owner_parent_name (owner_id, parent_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_folder ADD CONSTRAINT FK_MEDIA_FOLDER_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_folder ADD CONSTRAINT FK_MEDIA_FOLDER_PARENT FOREIGN KEY (parent_id) REFERENCES media_folder (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE media ADD folder_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql("INSERT INTO media_folder (id, owner_id, parent_id, name, created_at, updated_at) SELECT UUID_TO_BIN(UUID(), 1), m.owner_id, NULL, 'root', NOW(), NOW() FROM media m LEFT JOIN media_folder mf ON mf.owner_id = m.owner_id AND mf.parent_id IS NULL AND mf.name = 'root' WHERE m.owner_id IS NOT NULL AND mf.id IS NULL GROUP BY m.owner_id");
        $this->addSql("UPDATE media m INNER JOIN media_folder mf ON mf.owner_id = m.owner_id AND mf.parent_id IS NULL AND mf.name = 'root' SET m.folder_id = mf.id WHERE m.folder_id IS NULL AND m.owner_id IS NOT NULL");
        $this->addSql('CREATE INDEX idx_media_folder_id ON media (folder_id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_MEDIA_FOLDER FOREIGN KEY (folder_id) REFERENCES media_folder (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_MEDIA_FOLDER');
        $this->addSql('DROP INDEX idx_media_folder_id ON media');
        $this->addSql('ALTER TABLE media DROP folder_id');

        $this->addSql('ALTER TABLE media_folder DROP FOREIGN KEY FK_MEDIA_FOLDER_PARENT');
        $this->addSql('ALTER TABLE media_folder DROP FOREIGN KEY FK_MEDIA_FOLDER_OWNER');
        $this->addSql('DROP TABLE media_folder');
    }
}
