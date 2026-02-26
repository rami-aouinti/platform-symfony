<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260626110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalise status values for company/company_membership/media/offer/candidate_profile and prepare enum mapping.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE company SET status = 'active' WHERE status NOT IN ('active', 'suspended') OR status IS NULL");
        $this->addSql("UPDATE company_membership SET status = 'invited' WHERE status NOT IN ('invited', 'active') OR status IS NULL");
        $this->addSql("UPDATE media SET status = 'active' WHERE status NOT IN ('active', 'archived') OR status IS NULL");
        $this->addSql("UPDATE offer SET status = 'draft' WHERE status NOT IN ('draft', 'published', 'archived') OR status IS NULL");
        $this->addSql("UPDATE candidate_profile SET status = 'new' WHERE status NOT IN ('new', 'active', 'archived') OR status IS NULL");
    }

    public function down(Schema $schema): void
    {
    }
}
