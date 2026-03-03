<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace configuration uniqueness on profile scope with user_application/code/key_name scope';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uq_configuration_profile_key ON configuration');
        $this->addSql('DROP INDEX uq_configuration_user_application_key ON configuration');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_user_application_code_key ON configuration (user_application_id, code, key_name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uq_configuration_user_application_code_key ON configuration');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_user_application_key ON configuration (user_application_id, key_name)');
        $this->addSql('CREATE UNIQUE INDEX uq_configuration_profile_key ON configuration (profile_id, key_name)');
    }
}
