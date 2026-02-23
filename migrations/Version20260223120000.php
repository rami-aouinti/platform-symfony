<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

/**
 * Create user_profile table
 */
final class Version20260223120000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Create user_profile table with one-to-one relation to user.';
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

        $this->addSql('CREATE TABLE user_profile (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', photo VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, birth_date DATE DEFAULT NULL, bio LONGTEXT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, contacts JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'Datetime when entity was created\', updated_at DATETIME NOT NULL COMMENT \'Datetime when entity was updated\', UNIQUE INDEX UNIQ_8D93D649A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_8D93D649A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE user_profile');
    }
}
