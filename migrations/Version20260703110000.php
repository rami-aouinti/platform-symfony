<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description column to application catalog table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE application ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE application DROP description');
    }
}
