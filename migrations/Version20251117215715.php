<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117215715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figurine ADD COLUMN image_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__figurine AS SELECT id, vitrine_id, nom FROM figurine');
        $this->addSql('DROP TABLE figurine');
        $this->addSql('CREATE TABLE figurine (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vitrine_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, CONSTRAINT FK_9DD647827F17893 FOREIGN KEY (vitrine_id) REFERENCES vitrine (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO figurine (id, vitrine_id, nom) SELECT id, vitrine_id, nom FROM __temp__figurine');
        $this->addSql('DROP TABLE __temp__figurine');
        $this->addSql('CREATE INDEX IDX_9DD647827F17893 ON figurine (vitrine_id)');
    }
}
