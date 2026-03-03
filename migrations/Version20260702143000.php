<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user_application pivot table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE user_application (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', application_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_user_application_user_id (user_id), INDEX idx_user_application_application_id (application_id), INDEX idx_user_application_active (active), UNIQUE INDEX uq_user_application_user_application (user_id, application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE user_application ADD CONSTRAINT FK_D8A128A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_application ADD CONSTRAINT FK_D8A128A83E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_application DROP FOREIGN KEY FK_D8A128A8A76ED395');
        $this->addSql('ALTER TABLE user_application DROP FOREIGN KEY FK_D8A128A83E030ACD');
        $this->addSql('DROP TABLE user_application');
    }
}
