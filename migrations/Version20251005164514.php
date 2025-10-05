<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005164514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pro (id SERIAL NOT NULL, utilisateur_id INT DEFAULT NULL, description TEXT DEFAULT NULL, diplome VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BB4D6FFFB88E14F ON pro (utilisateur_id)');
        $this->addSql('COMMENT ON COLUMN pro.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE pro ADD CONSTRAINT FK_6BB4D6FFFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pro DROP CONSTRAINT FK_6BB4D6FFFB88E14F');
        $this->addSql('DROP TABLE pro');
    }
}
