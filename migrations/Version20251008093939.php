<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008093939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE schedules_pro (id SERIAL NOT NULL, pro_id INT DEFAULT NULL, day VARCHAR(255) DEFAULT NULL, morning_start VARCHAR(255) DEFAULT NULL, morning_end VARCHAR(255) DEFAULT NULL, afternoon_start VARCHAR(255) DEFAULT NULL, afternoon_end VARCHAR(255) DEFAULT NULL, closed VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C2A62D5BC3B7E4BA ON schedules_pro (pro_id)');
        $this->addSql('ALTER TABLE schedules_pro ADD CONSTRAINT FK_C2A62D5BC3B7E4BA FOREIGN KEY (pro_id) REFERENCES pro (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE schedules_pro DROP CONSTRAINT FK_C2A62D5BC3B7E4BA');
        $this->addSql('DROP TABLE schedules_pro');
    }
}
