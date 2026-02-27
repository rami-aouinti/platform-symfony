<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260702100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database-level constraints for quiz difficulty/category/time range consistency.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE quiz ADD CONSTRAINT chk_quiz_category CHECK (category IN ('general', 'science', 'history', 'sports', 'technology', 'entertainment'))");
        $this->addSql("ALTER TABLE quiz ADD CONSTRAINT chk_quiz_difficulty CHECK (difficulty IN ('easy', 'medium', 'hard'))");
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT chk_quiz_time_limit_positive CHECK (time_limit IS NULL OR time_limit > 0)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT chk_quiz_schedule CHECK (starts_at IS NULL OR ends_at IS NULL OR starts_at <= ends_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quiz DROP CHECK chk_quiz_schedule');
        $this->addSql('ALTER TABLE quiz DROP CHECK chk_quiz_time_limit_positive');
        $this->addSql('ALTER TABLE quiz DROP CHECK chk_quiz_difficulty');
        $this->addSql('ALTER TABLE quiz DROP CHECK chk_quiz_category');
    }
}
