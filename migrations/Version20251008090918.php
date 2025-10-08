<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008090918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pro ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD siren VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD siret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pro ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN pro.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pro DROP city');
        $this->addSql('ALTER TABLE pro DROP address');
        $this->addSql('ALTER TABLE pro DROP siren');
        $this->addSql('ALTER TABLE pro DROP siret');
        $this->addSql('ALTER TABLE pro DROP email');
        $this->addSql('ALTER TABLE pro DROP phone');
        $this->addSql('ALTER TABLE pro DROP title');
        $this->addSql('ALTER TABLE pro DROP updated_at');
    }
}
