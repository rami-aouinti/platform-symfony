<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260628100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Link project to company and make relation mandatory.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD company_id BINARY(16) DEFAULT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql('UPDATE project p SET company_id = (SELECT c.id FROM company c WHERE c.owner_id = p.owner_id ORDER BY c.created_at ASC LIMIT 1) WHERE p.owner_id IS NOT NULL');
        $this->addSql('UPDATE project p SET company_id = (SELECT c.id FROM company c ORDER BY c.created_at ASC LIMIT 1) WHERE p.company_id IS NULL');
        $this->addSql('ALTER TABLE project CHANGE company_id company_id BINARY(16) NOT NULL COMMENT "(DC2Type:uuid_binary_ordered_time)"');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idx_project_company_id ON project (company_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE979B1AD6');
        $this->addSql('DROP INDEX idx_project_company_id ON project');
        $this->addSql('ALTER TABLE project DROP company_id');
    }
}
