<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210109103202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD author_id INT DEFAULT NULL, ADD registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD account_must_be_verifeid_before DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD register_token VARCHAR(255) DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD account_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD forgot_password_token VARCHAR(255) DEFAULT NULL, ADD forgot_password_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD forgot_password_token_must_be_verified_before DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD forgot_password_token_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F675F31B FOREIGN KEY (author_id) REFERENCES authors (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F675F31B ON users (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9F675F31B');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F675F31B ON users');
        $this->addSql('ALTER TABLE users DROP author_id, DROP registered_at, DROP account_must_be_verifeid_before, DROP register_token, DROP is_verified, DROP account_verified_at, DROP forgot_password_token, DROP forgot_password_token_requested_at, DROP forgot_password_token_must_be_verified_before, DROP forgot_password_token_verified_at');
    }
}
