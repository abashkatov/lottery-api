<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027230812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bid_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bid (id INT NOT NULL, lot_id INT NOT NULL, bidder_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, bid INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4AF2B3F3A8CBA5F7 ON bid (lot_id)');
        $this->addSql('COMMENT ON COLUMN bid.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F3A8CBA5F7 FOREIGN KEY (lot_id) REFERENCES lot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lot ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE lot ADD current_bid INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bid_id_seq CASCADE');
        $this->addSql('ALTER TABLE bid DROP CONSTRAINT FK_4AF2B3F3A8CBA5F7');
        $this->addSql('DROP TABLE bid');
        $this->addSql('ALTER TABLE lot DROP created_at');
        $this->addSql('ALTER TABLE lot DROP current_bid');
    }
}
