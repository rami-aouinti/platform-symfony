<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add friend requests module with acceptance workflow.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE friend_request (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', requester_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', addressee_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', status VARCHAR(16) NOT NULL, accepted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_friend_request_addressee_status (addressee_id, status), INDEX idx_friend_request_requester_status (requester_id, status), UNIQUE INDEX uq_friend_request_requester_addressee (requester_id, addressee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_FRIEND_REQUEST_REQUESTER FOREIGN KEY (requester_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_FRIEND_REQUEST_ADDRESSEE FOREIGN KEY (addressee_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_FRIEND_REQUEST_REQUESTER');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_FRIEND_REQUEST_ADDRESSEE');
        $this->addSql('DROP TABLE friend_request');
    }
}
