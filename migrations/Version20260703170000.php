<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow multiple user applications per application and add user-specific metadata';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE user_application ADD name VARCHAR(255) DEFAULT NULL, ADD logo VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL");
        $this->addSql('UPDATE user_application ua INNER JOIN application a ON ua.application_id = a.id SET ua.name = a.name, ua.logo = a.logo, ua.description = a.description WHERE ua.name IS NULL');
        $this->addSql('ALTER TABLE user_application MODIFY name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX uq_user_application_user_application ON user_application');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX uq_user_application_user_application ON user_application (user_id, application_id)');
        $this->addSql('ALTER TABLE user_application DROP name, DROP logo, DROP description');
    }
}
