<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028083054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id BINARY(16) NOT NULL, message LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, job_id BINARY(16) NOT NULL, freelancer_id BINARY(16) NOT NULL, INDEX IDX_A45BDDC1BE04EA9 (job_id), INDEX IDX_A45BDDC18545BDF5 (freelancer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contract (id BINARY(16) NOT NULL, contract_address VARCHAR(255) NOT NULL, amount NUMERIC(18, 8) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(20) NOT NULL, signed_by_freelancer TINYINT(1) DEFAULT NULL, signed_by_recruiter TINYINT(1) DEFAULT NULL, signed_by_dao TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, recruiter_id BINARY(16) NOT NULL, freelancer_id BINARY(16) NOT NULL, job_id BINARY(16) NOT NULL, INDEX IDX_E98F2859156BE243 (recruiter_id), INDEX IDX_E98F28598545BDF5 (freelancer_id), INDEX IDX_E98F2859BE04EA9 (job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE job (id BINARY(16) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(255) DEFAULT NULL, duration VARCHAR(100) DEFAULT NULL, skills JSON DEFAULT NULL, budget NUMERIC(18, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, recruiter_id BINARY(16) NOT NULL, INDEX IDX_FBD8E0F8156BE243 (recruiter_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL, wallet_address VARCHAR(255) NOT NULL, username VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, user_token VARCHAR(255) DEFAULT NULL, network VARCHAR(20) NOT NULL, sol_balance NUMERIC(18, 8) DEFAULT NULL, eth_balance NUMERIC(18, 8) DEFAULT NULL, work_balance NUMERIC(18, 8) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6497C4F4A97 (wallet_address), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC18545BDF5 FOREIGN KEY (freelancer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859156BE243 FOREIGN KEY (recruiter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28598545BDF5 FOREIGN KEY (freelancer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8156BE243 FOREIGN KEY (recruiter_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1BE04EA9');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC18545BDF5');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859156BE243');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F28598545BDF5');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859BE04EA9');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8156BE243');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE user');
    }
}
