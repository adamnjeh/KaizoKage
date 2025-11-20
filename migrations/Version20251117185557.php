<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117185557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE galerie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, published BOOLEAN NOT NULL, CONSTRAINT FK_9E7D159061220EA6 FOREIGN KEY (creator_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9E7D159061220EA6 ON galerie (creator_id)');
        $this->addSql('CREATE TABLE galerie_figurine (galerie_id INTEGER NOT NULL, figurine_id INTEGER NOT NULL, PRIMARY KEY(galerie_id, figurine_id), CONSTRAINT FK_73009AB1825396CB FOREIGN KEY (galerie_id) REFERENCES galerie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_73009AB1C550FC1B FOREIGN KEY (figurine_id) REFERENCES figurine (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_73009AB1825396CB ON galerie_figurine (galerie_id)');
        $this->addSql('CREATE INDEX IDX_73009AB1C550FC1B ON galerie_figurine (figurine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE galerie');
        $this->addSql('DROP TABLE galerie_figurine');
    }
}
