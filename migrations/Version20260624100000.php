<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260624100000 extends AbstractMigration
{
    #[Override]
    public function getDescription(): string
    {
        return 'Add chat module tables (conversation, participants, messages) linked to accepted job applications.';
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

        $this->addSql('CREATE TABLE chat_conversation (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", job_application_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", UNIQUE INDEX uq_chat_conversation_job_application (job_application_id), INDEX idx_chat_conversation_job_application_id (job_application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_conversation_participant (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", conversation_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", user_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", UNIQUE INDEX uq_chat_conversation_participant_conversation_user (conversation_id, user_id), INDEX idx_chat_conversation_participant_conversation_id (conversation_id), INDEX idx_chat_conversation_participant_user_id (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_message (id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", conversation_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", sender_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)", content LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", updated_at DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX idx_chat_message_conversation_id (conversation_id), INDEX idx_chat_message_created_at (created_at), INDEX idx_chat_message_sender_id (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE chat_conversation ADD CONSTRAINT FK_CHAT_CONVERSATION_JOB_APPLICATION FOREIGN KEY (job_application_id) REFERENCES job_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_conversation_participant ADD CONSTRAINT FK_CHAT_PARTICIPANT_CONVERSATION FOREIGN KEY (conversation_id) REFERENCES chat_conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_conversation_participant ADD CONSTRAINT FK_CHAT_PARTICIPANT_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_CHAT_MESSAGE_CONVERSATION FOREIGN KEY (conversation_id) REFERENCES chat_conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_CHAT_MESSAGE_SENDER FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_CHAT_MESSAGE_CONVERSATION');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_CHAT_MESSAGE_SENDER');
        $this->addSql('ALTER TABLE chat_conversation_participant DROP FOREIGN KEY FK_CHAT_PARTICIPANT_CONVERSATION');
        $this->addSql('ALTER TABLE chat_conversation_participant DROP FOREIGN KEY FK_CHAT_PARTICIPANT_USER');
        $this->addSql('ALTER TABLE chat_conversation DROP FOREIGN KEY FK_CHAT_CONVERSATION_JOB_APPLICATION');

        $this->addSql('DROP TABLE chat_message');
        $this->addSql('DROP TABLE chat_conversation_participant');
        $this->addSql('DROP TABLE chat_conversation');
    }
}
