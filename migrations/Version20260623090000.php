<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623090000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add user social account table for social providers login mapping.';
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

        $this->addSql('CREATE TABLE user_social_account (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', provider VARCHAR(32) NOT NULL, provider_user_id VARCHAR(255) NOT NULL, provider_email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX uq_social_provider_user (provider, provider_user_id), UNIQUE INDEX uq_user_provider (user_id, provider), INDEX IDX_B4ABBE13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_social_account ADD CONSTRAINT FK_B4ABBE13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE user_social_account');
    }
}
