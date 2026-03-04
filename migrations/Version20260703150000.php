<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add page module tables: contact, about and faq';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE page_about (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', UNIQUE INDEX uq_page_about_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page_contact (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, template_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', INDEX idx_page_contact_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page_faq (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, display_order INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:utc_datetime)', INDEX idx_page_faq_order (display_order), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE page_faq');
        $this->addSql('DROP TABLE page_contact');
        $this->addSql('DROP TABLE page_about');
    }
}
