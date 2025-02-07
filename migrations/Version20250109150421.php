<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109150421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY ajouter_favoris0_FK');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY ajouter_Client_FK');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY ajouter_annonce1_FK');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY annonce_ibfk_1');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY annonce_ibfk_2');
        $this->addSql('ALTER TABLE avoir DROP FOREIGN KEY avoir_annonce0_FK');
        $this->addSql('ALTER TABLE avoir DROP FOREIGN KEY avoir_images_FK');
        $this->addSql('ALTER TABLE envoyer DROP FOREIGN KEY envoyer_messages_FK');
        $this->addSql('ALTER TABLE envoyer DROP FOREIGN KEY envoyer_Client0_FK');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY favoris_ibfk_1');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY favoris_ibfk_2');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY images_ibfk_1');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY messages_ibfk_3');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY messages_ibfk_1');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY messages_ibfk_2');
        $this->addSql('ALTER TABLE publier DROP FOREIGN KEY publier_Client0_FK');
        $this->addSql('ALTER TABLE publier DROP FOREIGN KEY publier_annonce_FK');
        $this->addSql('ALTER TABLE recevoir DROP FOREIGN KEY recevoir_annonce0_FK');
        $this->addSql('ALTER TABLE recevoir DROP FOREIGN KEY recevoir_messages_FK');
        $this->addSql('DROP TABLE ajouter');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE avoir');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE envoyer');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE publier');
        $this->addSql('DROP TABLE recevoir');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ajouter (id_client INT NOT NULL, id_favori INT NOT NULL, id_annonce INT NOT NULL, INDEX ajouter_favoris0_FK (id_favori), INDEX ajouter_annonce1_FK (id_annonce), INDEX IDX_AB384B5FE173B1B8 (id_client), PRIMARY KEY(id_client, id_favori, id_annonce)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE annonce (id_annonce INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, prix NUMERIC(10, 0) NOT NULL, localisation VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, status ENUM(\'en cours\', \'expédiée\', \'livrée\', \'annulée\') CHARACTER SET utf8mb4 DEFAULT \'\'\'en cours\'\'\' COLLATE `utf8mb4_general_ci`, date_ajout DATETIME NOT NULL, date_modif DATETIME DEFAULT \'NULL\', id_client INT NOT NULL, id_categorie INT NOT NULL, INDEX id_client (id_client), INDEX id_categorie (id_categorie), PRIMARY KEY(id_annonce)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE avoir (id_image INT NOT NULL, id_annonce INT NOT NULL, INDEX avoir_annonce0_FK (id_annonce), INDEX IDX_659B1A432BB8456F (id_image), PRIMARY KEY(id_image, id_annonce)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categorie (id_categorie INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_categorie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE client (id_client INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, mail VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, mdp VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, derniere_connexion DATETIME DEFAULT \'current_timestamp()\' NOT NULL, rue VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, cp VARCHAR(12) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ville VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, pays VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_client)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE envoyer (id_message INT NOT NULL, id_client INT NOT NULL, INDEX envoyer_Client0_FK (id_client), INDEX IDX_9E6AFC016820990F (id_message), PRIMARY KEY(id_message, id_client)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favoris (id_favori INT AUTO_INCREMENT NOT NULL, date_creation DATETIME NOT NULL, id_client INT NOT NULL, id_annonce INT NOT NULL, INDEX id_annonce (id_annonce), INDEX id_client (id_client), PRIMARY KEY(id_favori)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE images (id_image INT AUTO_INCREMENT NOT NULL, image_chemin VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME DEFAULT \'current_timestamp()\' NOT NULL, id_annonce INT NOT NULL, INDEX id_annonce (id_annonce), PRIMARY KEY(id_image)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messages (id_message INT AUTO_INCREMENT NOT NULL, message_contenant TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME NOT NULL, id_annonce INT NOT NULL, id_acheteur INT NOT NULL, id_vendeur INT NOT NULL, INDEX id_vendeur (id_vendeur), INDEX id_annonce (id_annonce), INDEX id_acheteur (id_acheteur), PRIMARY KEY(id_message)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE publier (id_annonce INT NOT NULL, id_client INT NOT NULL, INDEX publier_Client0_FK (id_client), INDEX IDX_596E80EC28C83A95 (id_annonce), PRIMARY KEY(id_annonce, id_client)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE recevoir (id_message INT NOT NULL, id_annonce INT NOT NULL, INDEX recevoir_annonce0_FK (id_annonce), INDEX IDX_8A801CAC6820990F (id_message), PRIMARY KEY(id_message, id_annonce)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT ajouter_favoris0_FK FOREIGN KEY (id_favori) REFERENCES favoris (id_favori)');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT ajouter_Client_FK FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT ajouter_annonce1_FK FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT annonce_ibfk_1 FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT annonce_ibfk_2 FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
        $this->addSql('ALTER TABLE avoir ADD CONSTRAINT avoir_annonce0_FK FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE avoir ADD CONSTRAINT avoir_images_FK FOREIGN KEY (id_image) REFERENCES images (id_image)');
        $this->addSql('ALTER TABLE envoyer ADD CONSTRAINT envoyer_messages_FK FOREIGN KEY (id_message) REFERENCES messages (id_message)');
        $this->addSql('ALTER TABLE envoyer ADD CONSTRAINT envoyer_Client0_FK FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT favoris_ibfk_1 FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT favoris_ibfk_2 FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT images_ibfk_1 FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT messages_ibfk_3 FOREIGN KEY (id_vendeur) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT messages_ibfk_1 FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT messages_ibfk_2 FOREIGN KEY (id_acheteur) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE publier ADD CONSTRAINT publier_Client0_FK FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE publier ADD CONSTRAINT publier_annonce_FK FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE recevoir ADD CONSTRAINT recevoir_annonce0_FK FOREIGN KEY (id_annonce) REFERENCES annonce (id_annonce)');
        $this->addSql('ALTER TABLE recevoir ADD CONSTRAINT recevoir_messages_FK FOREIGN KEY (id_message) REFERENCES messages (id_message)');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
