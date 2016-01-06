<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160106103017 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE webhook (id INT AUTO_INCREMENT NOT NULL, repository_id INT NOT NULL, name VARCHAR(50) NOT NULL, url VARCHAR(255) NOT NULL, last_call DATETIME DEFAULT NULL, last_status VARCHAR(50) DEFAULT NULL, INDEX IDX_8A74175650C9D4F7 (repository_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repository (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, private TINYINT(1) NOT NULL, stars INT NOT NULL, pulls INT NOT NULL, INDEX IDX_5CFE57CD7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layer (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(255) NOT NULL, digest VARCHAR(255) DEFAULT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manifest (id INT AUTO_INCREMENT NOT NULL, repository_id INT NOT NULL, tag VARCHAR(255) NOT NULL, digest VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, pulls INT NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A6FA684050C9D4F7 (repository_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webhook ADD CONSTRAINT FK_8A74175650C9D4F7 FOREIGN KEY (repository_id) REFERENCES repository (id)');
        $this->addSql('ALTER TABLE repository ADD CONSTRAINT FK_5CFE57CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manifest ADD CONSTRAINT FK_A6FA684050C9D4F7 FOREIGN KEY (repository_id) REFERENCES repository (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE webhook DROP FOREIGN KEY FK_8A74175650C9D4F7');
        $this->addSql('ALTER TABLE manifest DROP FOREIGN KEY FK_A6FA684050C9D4F7');
        $this->addSql('ALTER TABLE repository DROP FOREIGN KEY FK_5CFE57CD7E3C61F9');
        $this->addSql('DROP TABLE webhook');
        $this->addSql('DROP TABLE repository');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE layer');
        $this->addSql('DROP TABLE manifest');
    }
}
