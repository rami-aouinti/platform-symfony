<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260701120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create calendar_event table for calendar module CRUD.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE calendar_event (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", user_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, start_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", end_at DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", is_all_day TINYINT(1) NOT NULL DEFAULT 0, timezone VARCHAR(64) DEFAULT NULL, status VARCHAR(64) NOT NULL DEFAULT "confirmed", visibility VARCHAR(32) NOT NULL DEFAULT "public", is_cancelled TINYINT(1) NOT NULL DEFAULT 0, url VARCHAR(2048) DEFAULT NULL, color VARCHAR(32) DEFAULT NULL, background_color VARCHAR(32) DEFAULT NULL, border_color VARCHAR(32) DEFAULT NULL, text_color VARCHAR(32) DEFAULT NULL, organizer_name VARCHAR(255) DEFAULT NULL, organizer_email VARCHAR(255) DEFAULT NULL, attendees JSON DEFAULT NULL, rrule LONGTEXT DEFAULT NULL, recurrence_exceptions JSON DEFAULT NULL, recurrence_end_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", recurrence_count INT DEFAULT NULL, reminders JSON DEFAULT NULL, metadata JSON DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_calendar_event_start_at (start_at), INDEX idx_calendar_event_end_at (end_at), INDEX idx_calendar_event_status (status), INDEX idx_calendar_event_visibility (visibility), INDEX idx_calendar_event_user_id (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar_event ADD CONSTRAINT FK_5A5B8153A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE calendar_event DROP FOREIGN KEY FK_5A5B8153A76ED395');
        $this->addSql('DROP TABLE calendar_event');
    }
}
