<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260629100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add photo URL and media ID fields to company and project.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD photo_url VARCHAR(2048) DEFAULT NULL, ADD photo_media_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD photo_url VARCHAR(2048) DEFAULT NULL, ADD photo_media_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company DROP photo_url, DROP photo_media_id');
        $this->addSql('ALTER TABLE project DROP photo_url, DROP photo_media_id');
    }
}
