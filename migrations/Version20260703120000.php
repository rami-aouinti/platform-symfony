<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_application scope to configuration and backfill unambiguous existing records';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE configuration ADD user_application_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)'");
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_CONFIGURATION_USER_APPLICATION FOREIGN KEY (user_application_id) REFERENCES user_application (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_configuration_user_application ON configuration (user_application_id)');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_user_application_key ON configuration (user_application_id, key_name)');

        $this->addSql(<<<'SQL'
            UPDATE configuration c
            INNER JOIN user_profile up ON up.id = c.profile_id
            INNER JOIN (
                SELECT ua.user_id, MIN(ua.id) AS selected_user_application_id
                FROM user_application ua
                WHERE ua.active = 1
                GROUP BY ua.user_id
                HAVING COUNT(*) = 1
            ) ua_unique ON ua_unique.user_id = up.user_id
            SET c.user_application_id = ua_unique.selected_user_application_id
            WHERE c.profile_id IS NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uq_configuration_user_application_key ON configuration');
        $this->addSql('DROP INDEX idx_configuration_user_application ON configuration');
        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_CONFIGURATION_USER_APPLICATION');
        $this->addSql('ALTER TABLE configuration DROP user_application_id');
    }
}
