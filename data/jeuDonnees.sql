-- ==========================================================
-- 1. NETTOYAGE
-- ==========================================================
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE inscription; TRUNCATE TABLE sortie; TRUNCATE TABLE user;
TRUNCATE TABLE lieu; TRUNCATE TABLE ville; TRUNCATE TABLE site; TRUNCATE TABLE etat;
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================================
-- 2. RÉFÉRENTIEL
-- ==========================================================
INSERT INTO etat (id, libelle, code) VALUES
                                         (1, 'En Création', 'CRE'), (2, 'Inscriptions ouvertes', 'OUV'),
                                         (3, 'Inscriptions Clôturées', 'CLO'), (4, 'Activité en cours', 'EC'),
                                         (5, 'Annulée', 'ANN'), (6, 'Passée', 'FIN'), (7, 'Archivée', 'ARCH');

INSERT INTO site (nom) VALUES ('Nantes'), ('Rennes'), ('Quimper'), ('Niort');

INSERT INTO ville (nom, code_postal) VALUES
                                         ('Nantes', '44000'), ('Rennes', '35000'), ('Saint-Herblain', '44800'), ('Bruz', '35170');

INSERT INTO lieu (id, nom, rue, ville_id) VALUES
                                              (1, 'Bowling Central', 'Place du Commerce', 1),
                                              (2, 'Parc de Thabor', 'Rue de Paris', 2),
                                              (3, 'Patinoire Atlantis', 'Bd Salvador Allende', 3),
                                              (4, 'Cinéma Gaumont', 'Quai Duguay Trouin', 2),
                                              (5, 'Piscine Léo Lagrange', 'Rue d''Alger', 1);

-- ==========================================================
-- 3. UTILISATEURS (Découpés pour éviter les erreurs de buffer)
-- ==========================================================
INSERT INTO user (id, username, roles, password, nom, prenom, mail, actif, site_id, telephone) VALUES
                                                                                                   (1, 'admin', '["ROLE_ADMIN"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Admin', 'Joe', 'admin@test.fr', 1, 1, '0240060301'),
                                                                                                   (2, 'user1', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Dupont', 'Jean', 'u1@test.fr', 1, 1, '0240060302'),
                                                                                                   (3, 'user2', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Martin', 'Anne', 'u2@test.fr', 1, 1, '0240060303'),
                                                                                                   (4, 'user3', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Morel', 'Luc', 'u3@test.fr', 1, 2, '0240060304'),
                                                                                                   (5, 'user4', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Petit', 'Julie', 'u4@test.fr', 1, 2, '0240060305'),
                                                                                                   (6, 'user5', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Rousseau', 'Marc', 'u5@test.fr', 1, 3, '0240060306'),
                                                                                                   (7, 'user6', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Lefebvre', 'Clara', 'u6@test.fr', 1, 3, '0240060307'),
                                                                                                   (8, 'user7', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Garcia', 'Luis', 'u7@test.fr', 1, 4, '0240060308'),
                                                                                                   (9, 'user8', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Bertrand', 'Sonia', 'u8@test.fr', 1, 4, '0240060309'),
                                                                                                   (10, 'user9', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Girard', 'Paul', 'u9@test.fr', 1, 1, '0240060310');

INSERT INTO user (id, username, roles, password, nom, prenom, mail, actif, site_id, telephone) VALUES
                                                                                                   (11, 'user10', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Andre', 'Lea', 'u10@test.fr', 1, 1, '0240060311'),
                                                                                                   (12, 'user11', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Lefevre', 'Thomas', 'u11@test.fr', 1, 2, '0240060312'),
                                                                                                   (13, 'user12', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Mercier', 'Chloé', 'u12@test.fr', 1, 2, '0240060313'),
                                                                                                   (14, 'user13', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Bonnet', 'Eric', 'u13@test.fr', 1, 3, '0240060314'),
                                                                                                   (15, 'user14', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'François', 'Emma', 'u14@test.fr', 1, 3, '0240060315'),
                                                                                                   (16, 'user15', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Legrand', 'Hugo', 'u15@test.fr', 1, 4, '0240060316'),
                                                                                                   (17, 'user16', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Garnier', 'Alice', 'u16@test.fr', 1, 4, '0240060317'),
                                                                                                   (18, 'user17', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Faure', 'Yann', 'u17@test.fr', 1, 1, '0240060318'),
                                                                                                   (19, 'user18', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Roussel', 'Manon', 'u18@test.fr', 1, 1, '0240060319'),
                                                                                                   (20, 'user19', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Guerin', 'Théo', 'u19@test.fr', 1, 2, '0240060320'),
                                                                                                   (21, 'user_shadow', '["ROLE_USER"]', '$2y$13$yauNSTcTpX.FBy7s3T53C.f8LQmnyLB0rALltrsgkflpSOHHxZ3OO', 'Solo', 'Han', 'solo@test.fr', 1, 1, '0240060321');

-- ==========================================================
-- 4. SORTIES (50) - Organisées par IDs 1 à 20
-- ==========================================================
INSERT INTO sortie (id, nom, date_heure_debut, duree, date_limite_inscription, nb_inscriptions_max, etat_id, lieu_id, organisateur_id)
SELECT
    n,
    CASE
        WHEN (n % 5) = 0 THEN CONCAT('Tournoi Bowling #', n)
        WHEN (n % 5) = 1 THEN CONCAT('Pique-nique Parc #', n)
        WHEN (n % 5) = 2 THEN CONCAT('Session Patinoire #', n)
        WHEN (n % 5) = 3 THEN CONCAT('Ciné-Débat n°', n)
        ELSE CONCAT('Aquagym Session ', n)
        END,
    CASE
        WHEN n <= 10 THEN DATE_ADD(NOW(), INTERVAL (n + 5) DAY)
        WHEN n <= 20 THEN DATE_ADD(NOW(), INTERVAL 2 DAY)
        WHEN n <= 30 THEN DATE_SUB(NOW(), INTERVAL 1 HOUR)
        WHEN n <= 40 THEN DATE_SUB(NOW(), INTERVAL 2 DAY)
        ELSE DATE_ADD(NOW(), INTERVAL 20 DAY)
        END,
    120,
    CASE
        WHEN n <= 10 THEN DATE_ADD(NOW(), INTERVAL 2 DAY)
        WHEN n <= 20 THEN DATE_SUB(NOW(), INTERVAL 1 DAY)
        ELSE DATE_SUB(NOW(), INTERVAL 5 DAY)
        END,
    10,
    CASE
        WHEN n <= 10 THEN 2 -- OUV
        WHEN n <= 20 THEN 3 -- CLO
        WHEN n <= 30 THEN 4 -- EC
        WHEN n <= 40 THEN 6 -- FIN
        ELSE 1              -- CRE
        END,
    (n % 5) + 1,
    (n % 20) + 1
FROM (
         SELECT a.N + b.N * 10 + 1 AS n
         FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a
                  CROSS JOIN (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) b
     ) as numbers;

-- ==========================================================
-- 5. INSCRIPTIONS
-- ==========================================================
INSERT IGNORE INTO inscription (date_inscription, participant_id, sortie_id)
SELECT NOW(), u.id, s.id
FROM (SELECT id FROM user) u
         CROSS JOIN (SELECT id FROM sortie WHERE etat_id != 1) s
WHERE (u.id + s.id) % 7 = 0
LIMIT 150;
