<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260623113000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Create job-offer taxonomies and relation tables, and replace city/region free text columns with foreign keys.';
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

        $this->addSql('CREATE TABLE skill (id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", updated_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", UNIQUE INDEX uq_skill_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", code VARCHAR(8) NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", updated_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", UNIQUE INDEX uq_language_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", code VARCHAR(64) NOT NULL, name VARCHAR(128) NOT NULL, country_code VARCHAR(2) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", updated_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", UNIQUE INDEX uq_region_country_code (country_code, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", region_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", name VARCHAR(128) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", updated_at DATETIME DEFAULT NULL COMMENT \"(DC2Type:datetime_immutable)\", INDEX idx_city_region (region_id), UNIQUE INDEX uq_city_region_name (region_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023478260F6 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE job_offer_skill (job_offer_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", skill_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", INDEX idx_job_offer_skill_job_offer_id (job_offer_id), INDEX idx_job_offer_skill_skill_id (skill_id), PRIMARY KEY(job_offer_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer_language (job_offer_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", language_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", INDEX idx_job_offer_language_job_offer_id (job_offer_id), INDEX idx_job_offer_language_language_id (language_id), PRIMARY KEY(job_offer_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_offer_skill ADD CONSTRAINT FK_8A39F5FF4F6FB43E FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_skill ADD CONSTRAINT FK_8A39F5FF5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_language ADD CONSTRAINT FK_1AA647314F6FB43E FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_language ADD CONSTRAINT FK_1AA6473182F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE job_offer DROP INDEX idx_job_offer_city, DROP INDEX idx_job_offer_region, ADD city_id BINARY(16) DEFAULT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", ADD region_id BINARY(16) DEFAULT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\", DROP city, DROP region');
        $this->addSql('CREATE INDEX idx_job_offer_city_id ON job_offer (city_id)');
        $this->addSql('CREATE INDEX idx_job_offer_region_id ON job_offer (region_id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_EA5A54D68BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_EA5A54D698260F6 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE SET NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_EA5A54D68BAC62AF');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_EA5A54D698260F6');
        $this->addSql('DROP INDEX idx_job_offer_city_id ON job_offer');
        $this->addSql('DROP INDEX idx_job_offer_region_id ON job_offer');
        $this->addSql('ALTER TABLE job_offer ADD city VARCHAR(128) DEFAULT NULL, ADD region VARCHAR(128) DEFAULT NULL, DROP city_id, DROP region_id');
        $this->addSql('CREATE INDEX idx_job_offer_city ON job_offer (city)');
        $this->addSql('CREATE INDEX idx_job_offer_region ON job_offer (region)');

        $this->addSql('ALTER TABLE job_offer_language DROP FOREIGN KEY FK_1AA647314F6FB43E');
        $this->addSql('ALTER TABLE job_offer_language DROP FOREIGN KEY FK_1AA6473182F1BAF4');
        $this->addSql('ALTER TABLE job_offer_skill DROP FOREIGN KEY FK_8A39F5FF4F6FB43E');
        $this->addSql('ALTER TABLE job_offer_skill DROP FOREIGN KEY FK_8A39F5FF5585C142');
        $this->addSql('DROP TABLE job_offer_language');
        $this->addSql('DROP TABLE job_offer_skill');

        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B023478260F6');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE skill');
    }
}
