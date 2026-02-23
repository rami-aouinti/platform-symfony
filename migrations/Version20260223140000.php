<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260223140000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add company, company_membership and candidate_profile structures.';
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

        $this->addSql('CREATE TABLE company (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', legal_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(64) NOT NULL, main_address LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX uq_company_slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_membership (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', company_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', role VARCHAR(64) NOT NULL, status VARCHAR(64) NOT NULL, invited_at DATETIME DEFAULT NULL, joined_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_COMPANY_MEMBERSHIP_USER (user_id), INDEX IDX_COMPANY_MEMBERSHIP_COMPANY (company_id), UNIQUE INDEX uq_company_membership_user_company (user_id, company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_profile (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', company_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', status VARCHAR(64) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX uq_candidate_profile_user (user_id), INDEX IDX_CANDIDATE_PROFILE_COMPANY (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_membership ADD CONSTRAINT FK_COMPANY_MEMBERSHIP_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_membership ADD CONSTRAINT FK_COMPANY_MEMBERSHIP_COMPANY FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_profile ADD CONSTRAINT FK_CANDIDATE_PROFILE_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_profile ADD CONSTRAINT FK_CANDIDATE_PROFILE_COMPANY FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE candidate_profile DROP FOREIGN KEY FK_CANDIDATE_PROFILE_USER');
        $this->addSql('ALTER TABLE candidate_profile DROP FOREIGN KEY FK_CANDIDATE_PROFILE_COMPANY');
        $this->addSql('ALTER TABLE company_membership DROP FOREIGN KEY FK_COMPANY_MEMBERSHIP_USER');
        $this->addSql('ALTER TABLE company_membership DROP FOREIGN KEY FK_COMPANY_MEMBERSHIP_COMPANY');
        $this->addSql('DROP TABLE candidate_profile');
        $this->addSql('DROP TABLE company_membership');
        $this->addSql('DROP TABLE company');
    }
}
