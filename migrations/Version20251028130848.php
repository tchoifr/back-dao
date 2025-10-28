<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028130848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A8E26E9D17F50A6 ON conversation (uuid)');
        $this->addSql('ALTER TABLE message ADD time DATETIME NOT NULL, DROP created_at, CHANGE conversation_id conversation_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8A8E26E9D17F50A6 ON conversation');
        $this->addSql('ALTER TABLE message ADD created_at VARCHAR(255) NOT NULL, DROP time, CHANGE conversation_id conversation_id INT DEFAULT NULL');
    }
}
