<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260701100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attach sprint to company and add index on company_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sprint ADD company_id BINARY(16) NOT NULL COMMENT \"(DC2Type:uuid_binary_ordered_time)\"');
        $this->addSql('CREATE INDEX idx_sprint_company_id ON sprint (company_id)');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_BDCE0F8B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sprint DROP FOREIGN KEY FK_BDCE0F8B979B1AD6');
        $this->addSql('DROP INDEX idx_sprint_company_id ON sprint');
        $this->addSql('ALTER TABLE sprint DROP company_id');
    }
}
