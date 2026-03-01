<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260701110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Configuration.value to JSON and add optional relation to user_profile with key uniqueness per profile.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE configuration DROP INDEX uq_configuration_key');
        $this->addSql("ALTER TABLE configuration CHANGE value value JSON NOT NULL COMMENT '(DC2Type:json)'");
        $this->addSql("ALTER TABLE configuration ADD profile_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)'");
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_7656A6A7CCFA12B8 FOREIGN KEY (profile_id) REFERENCES user_profile (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_configuration_profile ON configuration (profile_id)');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_profile_key ON configuration (profile_id, key_name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_7656A6A7CCFA12B8');
        $this->addSql('DROP INDEX uq_configuration_profile_key ON configuration');
        $this->addSql('DROP INDEX idx_configuration_profile ON configuration');
        $this->addSql('ALTER TABLE configuration DROP profile_id');
        $this->addSql('ALTER TABLE configuration CHANGE value value LONGTEXT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_key ON configuration (key_name)');
    }
}
