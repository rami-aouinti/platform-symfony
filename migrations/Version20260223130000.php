<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

/**
 * Create address + user_avatar while keeping legacy user_profile columns for compatibility
 */
final class Version20260223130000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add dedicated address and user_avatar entities linked to user_profile (phase 1, keep legacy columns).';
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

        $this->addSql('CREATE TABLE address (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_profile_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', type VARCHAR(32) NOT NULL, street_line_1 VARCHAR(255) NOT NULL, street_line_2 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(32) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT NULL, country_code VARCHAR(2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'Datetime when entity was created\', updated_at DATETIME NOT NULL COMMENT \'Datetime when entity was updated\', INDEX IDX_D4E6F81ECFE6EFA9 (user_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_avatar (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_profile_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', media_id VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) NOT NULL, created_at DATETIME NOT NULL COMMENT \'Datetime when entity was created\', updated_at DATETIME NOT NULL COMMENT \'Datetime when entity was updated\', UNIQUE INDEX UNIQ_D10C4F52CFE6EFA9 (user_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81ECFE6EFA9 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_avatar ADD CONSTRAINT FK_D10C4F52CFE6EFA9 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE user_avatar DROP FOREIGN KEY FK_D10C4F52CFE6EFA9');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81ECFE6EFA9');
        $this->addSql('DROP TABLE user_avatar');
        $this->addSql('DROP TABLE address');
    }
}
