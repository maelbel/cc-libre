-- Reference schema generated from the Doctrine entity mappings (src/Entity).
-- This file mirrors exactly what migrations/Version20260101000000.php creates;
-- it exists so the schema can be read/reviewed without a working Doctrine
-- toolchain, and was validated by executing it against a real PostgreSQL 16
-- instance during development (see README "Verification effectuee").

CREATE TABLE cinema (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    rue VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    CONSTRAINT uniq_cinema_nom UNIQUE (nom)
);

CREATE TABLE salle (
    id SERIAL PRIMARY KEY,
    numero INT NOT NULL,
    capacite INT NOT NULL,
    date_construction DATE NOT NULL,
    cinema_id INT NOT NULL,
    CONSTRAINT fk_salle_cinema FOREIGN KEY (cinema_id) REFERENCES cinema (id) ON DELETE CASCADE,
    CONSTRAINT uniq_salle_cinema_numero UNIQUE (cinema_id, numero)
);
CREATE INDEX idx_salle_cinema ON salle (cinema_id);

CREATE TABLE horaire (
    id SERIAL PRIMARY KEY,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL
);

CREATE TABLE film (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    annee INT NOT NULL,
    genre VARCHAR(100) NOT NULL
);

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
);
CREATE INDEX idx_seance_date ON seance (date);
CREATE INDEX idx_seance_salle ON seance (salle_id);
CREATE INDEX idx_seance_film ON seance (film_id);
CREATE INDEX idx_seance_horaire ON seance (horaire_id);

CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    email VARCHAR(180) NOT NULL,
    roles JSONB NOT NULL,
    password VARCHAR(255) NOT NULL,
    CONSTRAINT uniq_user_email UNIQUE (email)
);

CREATE TABLE reset_password_request (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    selector VARCHAR(20) NOT NULL,
    hashed_token VARCHAR(100) NOT NULL,
    requested_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    CONSTRAINT fk_reset_password_user FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE
);
