<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200224152542 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rendezvous (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, datetime DATETIME NOT NULL, valide TINYINT(1) NOT NULL, INDEX IDX_C09A9BA86B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, adresse_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, cin INT NOT NULL, sexe VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, date_nais DATE NOT NULL, numtel INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\', image VARCHAR(255) NOT NULL, dtype VARCHAR(255) NOT NULL, numserieM INT DEFAULT NULL, numcarnet INT DEFAULT NULL, validite_carnet DATE DEFAULT NULL, group_sang VARCHAR(255) DEFAULT NULL, profession VARCHAR(255) DEFAULT NULL, etat_civile VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3ABE530DA (cin), UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), UNIQUE INDEX UNIQ_1D1C63B37434FDF5 (numtel), UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B34DE7DC5C (adresse_id), UNIQUE INDEX UNIQ_1D1C63B336CA416C (numserieM), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultations (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, medecin_id INT NOT NULL, date_c DATETIME NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_242D8F536B899279 (patient_id), INDEX IDX_242D8F534F31A84 (medecin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, adresse LONGTEXT NOT NULL, code_postal INT NOT NULL, ville VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C35F0816CC94AC37 (code_postal), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA86B899279 FOREIGN KEY (patient_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B34DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE consultations ADD CONSTRAINT FK_242D8F536B899279 FOREIGN KEY (patient_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE consultations ADD CONSTRAINT FK_242D8F534F31A84 FOREIGN KEY (medecin_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA86B899279');
        $this->addSql('ALTER TABLE consultations DROP FOREIGN KEY FK_242D8F536B899279');
        $this->addSql('ALTER TABLE consultations DROP FOREIGN KEY FK_242D8F534F31A84');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B34DE7DC5C');
        $this->addSql('DROP TABLE rendezvous');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE consultations');
        $this->addSql('DROP TABLE adresse');
    }
}
