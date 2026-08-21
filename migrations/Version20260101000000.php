<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial schema: Cinema, Salle, Horaire, Film, Seance, User,
 * ResetPasswordRequest. See migrations/schema.sql for the plain-SQL
 * equivalent (validated by hand against a real PostgreSQL 16 instance).
 */
final class Version20260101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schema initial du systeme de programmation cinema (Cinema, Salle, Horaire, Film, Seance, User, ResetPasswordRequest)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE cinema (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(150) NOT NULL,
                ville VARCHAR(100) NOT NULL,
                rue VARCHAR(150) NOT NULL,
                numero VARCHAR(20) NOT NULL,
                CONSTRAINT uniq_cinema_nom UNIQUE (nom)
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE salle (
                id SERIAL PRIMARY KEY,
                numero INT NOT NULL,
                capacite INT NOT NULL,
                date_construction DATE NOT NULL,
                cinema_id INT NOT NULL,
                CONSTRAINT fk_salle_cinema FOREIGN KEY (cinema_id) REFERENCES cinema (id) ON DELETE CASCADE,
                CONSTRAINT uniq_salle_cinema_numero UNIQUE (cinema_id, numero)
            )
        SQL);
        $this->addSql('CREATE INDEX idx_salle_cinema ON salle (cinema_id)');

        $this->addSql(<<<'SQL'
            CREATE TABLE horaire (
                id SERIAL PRIMARY KEY,
                heure_debut TIME NOT NULL,
                heure_fin TIME NOT NULL
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE film (
                id SERIAL PRIMARY KEY,
                titre VARCHAR(255) NOT NULL,
                annee INT NOT NULL,
                genre VARCHAR(100) NOT NULL
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE seance (
                id SERIAL PRIMARY KEY,
                date DATE NOT NULL,
                tarif NUMERIC(6, 2) NOT NULL,
                salle_id INT NOT NULL,
                film_id INT NOT NULL,
                horaire_id INT NOT NULL,
                CONSTRAINT fk_seance_salle FOREIGN KEY (salle_id) REFERENCES salle (id) ON DELETE CASCADE,
                CONSTRAINT fk_seance_film FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE,
                CONSTRAINT fk_seance_horaire FOREIGN KEY (horaire_id) REFERENCES horaire (id) ON DELETE RESTRICT
            )
        SQL);
        $this->addSql('CREATE INDEX idx_seance_date ON seance (date)');
        $this->addSql('CREATE INDEX idx_seance_salle ON seance (salle_id)');
        $this->addSql('CREATE INDEX idx_seance_film ON seance (film_id)');
        $this->addSql('CREATE INDEX idx_seance_horaire ON seance (horaire_id)');

        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (
                id SERIAL PRIMARY KEY,
                email VARCHAR(180) NOT NULL,
                roles JSONB NOT NULL,
                password VARCHAR(255) NOT NULL,
                CONSTRAINT uniq_user_email UNIQUE (email)
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL,
                selector VARCHAR(20) NOT NULL,
                hashed_token VARCHAR(100) NOT NULL,
                requested_at TIMESTAMP NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                CONSTRAINT fk_reset_password_user FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS reset_password_request');
        $this->addSql('DROP TABLE IF EXISTS seance');
        $this->addSql('DROP TABLE IF EXISTS film');
        $this->addSql('DROP TABLE IF EXISTS horaire');
        $this->addSql('DROP TABLE IF EXISTS salle');
        $this->addSql('DROP TABLE IF EXISTS cinema');
        $this->addSql('DROP TABLE IF EXISTS "user"');
    }
}
