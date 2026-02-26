<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Converge on transversal embeddable addresses for user address, job offer and company.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address CHANGE state region VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE job_offer ADD address_street_line_1 VARCHAR(255) DEFAULT NULL, ADD address_street_line_2 VARCHAR(255) DEFAULT NULL, ADD address_postal_code VARCHAR(32) DEFAULT NULL, ADD address_city VARCHAR(255) NOT NULL DEFAULT \'\', ADD address_region VARCHAR(255) DEFAULT NULL, ADD address_country_code VARCHAR(2) NOT NULL DEFAULT \'\'');
        $this->addSql('UPDATE job_offer SET address_city = COALESCE(city, location, \"\"), address_region = region, address_country_code = COALESCE(country, \"\")');
        $this->addSql('ALTER TABLE job_offer DROP city, DROP region, DROP country');

        $this->addSql('ALTER TABLE company ADD main_address_street_line_1 VARCHAR(255) DEFAULT NULL, ADD main_address_street_line_2 VARCHAR(255) DEFAULT NULL, ADD main_address_postal_code VARCHAR(32) DEFAULT NULL, ADD main_address_city VARCHAR(255) NOT NULL DEFAULT \'\', ADD main_address_region VARCHAR(255) DEFAULT NULL, ADD main_address_country_code VARCHAR(2) NOT NULL DEFAULT \'\'');
        $this->addSql('UPDATE company SET main_address_street_line_1 = main_address');
        $this->addSql('ALTER TABLE company DROP main_address');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address CHANGE region state VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE job_offer ADD city VARCHAR(255) DEFAULT NULL, ADD region VARCHAR(255) DEFAULT NULL, ADD country VARCHAR(2) DEFAULT NULL');
        $this->addSql('UPDATE job_offer SET city = address_city, region = address_region, country = address_country_code');
        $this->addSql('ALTER TABLE job_offer DROP address_street_line_1, DROP address_street_line_2, DROP address_postal_code, DROP address_city, DROP address_region, DROP address_country_code');

        $this->addSql('ALTER TABLE company ADD main_address LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE company SET main_address = main_address_street_line_1');
        $this->addSql('ALTER TABLE company DROP main_address_street_line_1, DROP main_address_street_line_2, DROP main_address_postal_code, DROP main_address_city, DROP main_address_region, DROP main_address_country_code');
    }
}
