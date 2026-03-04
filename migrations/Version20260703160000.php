<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add key and timestamps to application catalog and timestamps to plugin catalog';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE application ADD key_name VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("UPDATE application SET key_name = LOWER(REPLACE(name, ' ', '-')) WHERE key_name IS NULL");
        $this->addSql('CREATE UNIQUE INDEX uq_application_key_name ON application (key_name)');
        $this->addSql('ALTER TABLE application MODIFY key_name VARCHAR(255) NOT NULL');

        $this->addSql("ALTER TABLE plugin ADD created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE application DROP INDEX uq_application_key_name');
        $this->addSql('ALTER TABLE application DROP key_name, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE plugin DROP created_at, DROP updated_at');
    }
}
