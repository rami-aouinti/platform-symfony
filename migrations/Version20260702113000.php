<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add read status, attachments and reactions for chat messages.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE chat_message ADD read_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("ALTER TABLE chat_message ADD attachments JSON DEFAULT NULL");
        $this->addSql("UPDATE chat_message SET attachments = '[]' WHERE attachments IS NULL");
        $this->addSql("ALTER TABLE chat_message CHANGE attachments attachments JSON NOT NULL");
        $this->addSql("ALTER TABLE chat_message_reaction (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', message_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', reaction VARCHAR(32) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_chat_message_reaction_message_id (message_id), INDEX idx_chat_message_reaction_user_id (user_id), UNIQUE INDEX uq_chat_message_reaction_message_user_type (message_id, user_id, reaction), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE chat_message_reaction ADD CONSTRAINT FK_CHAT_MESSAGE_REACTION_MESSAGE FOREIGN KEY (message_id) REFERENCES chat_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message_reaction ADD CONSTRAINT FK_CHAT_MESSAGE_REACTION_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_message_reaction DROP FOREIGN KEY FK_CHAT_MESSAGE_REACTION_MESSAGE');
        $this->addSql('ALTER TABLE chat_message_reaction DROP FOREIGN KEY FK_CHAT_MESSAGE_REACTION_USER');
        $this->addSql('DROP TABLE chat_message_reaction');
        $this->addSql('ALTER TABLE chat_message DROP read_at, DROP attachments');
    }
}
