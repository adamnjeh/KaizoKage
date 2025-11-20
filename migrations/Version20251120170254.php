<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120170254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vitrine ADD COLUMN image_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__vitrine AS SELECT id, owner_id, description FROM vitrine');
        $this->addSql('DROP TABLE vitrine');
        $this->addSql('CREATE TABLE vitrine (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, CONSTRAINT FK_71EB69B47E3C61F9 FOREIGN KEY (owner_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO vitrine (id, owner_id, description) SELECT id, owner_id, description FROM __temp__vitrine');
        $this->addSql('DROP TABLE __temp__vitrine');
        $this->addSql('CREATE INDEX IDX_71EB69B47E3C61F9 ON vitrine (owner_id)');
    }
}
