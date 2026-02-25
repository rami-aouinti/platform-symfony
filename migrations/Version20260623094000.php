<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623094000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add owner relation to company table.';
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

        $this->addSql('ALTER TABLE company ADD owner_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\'');
        $this->addSql('CREATE INDEX IDX_4FBF094FA7E8D4B0 ON company (owner_id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA7E8D4B0 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FA7E8D4B0');
        $this->addSql('DROP INDEX IDX_4FBF094FA7E8D4B0 ON company');
        $this->addSql('ALTER TABLE company DROP owner_id');
    }
}
