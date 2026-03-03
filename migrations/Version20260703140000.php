<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add plugin catalog and user application plugin activation tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE plugin (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', key_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX uq_plugin_key_name (key_name), INDEX idx_plugin_active (active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user_application_plugin (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', user_application_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', plugin_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', INDEX idx_user_application_plugin_user_application_id (user_application_id), INDEX idx_user_application_plugin_plugin_id (plugin_id), INDEX idx_user_application_plugin_active (active), UNIQUE INDEX uq_user_application_plugin_ua_plugin (user_application_id, plugin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE user_application_plugin ADD CONSTRAINT FK_7A3AFA96F54A4402 FOREIGN KEY (user_application_id) REFERENCES user_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_application_plugin ADD CONSTRAINT FK_7A3AFA96EC942BC5 FOREIGN KEY (plugin_id) REFERENCES plugin (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_application_plugin DROP FOREIGN KEY FK_7A3AFA96F54A4402');
        $this->addSql('ALTER TABLE user_application_plugin DROP FOREIGN KEY FK_7A3AFA96EC942BC5');
        $this->addSql('DROP TABLE user_application_plugin');
        $this->addSql('DROP TABLE plugin');
    }
}
