<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create application catalog table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE application (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, INDEX idx_application_active (active), UNIQUE INDEX uq_application_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE application');
    }
}
