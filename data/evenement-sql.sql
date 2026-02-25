# PASSEE à l'état CLO : inscriptions cloturé
UPDATE sortie
SET etat_id	= (SELECT id FROM etat WHERE code = 'CLO' LIMIT 1)
WHERE (NOW() BETWEEN date_limite_inscription and date_heure_debut);


UPDATE sortie s
    INNER JOIN (
        SELECT sortie_id, COUNT(*) as total
        FROM inscription
        GROUP BY sortie_id
    ) i ON s.id = i.sortie_id
SET s.etat_id = (SELECT id FROM etat WHERE code = 'CLO' LIMIT 1)
WHERE i.total >= s.nb_inscriptions_max;


# PASSEE à l'état EC : activité en cours
UPDATE sortie
SET etat_id	= (SELECT id FROM etat WHERE code = 'EC' LIMIT 1)
WHERE NOW() BETWEEN  date_heure_debut and DATE_ADD(date_heure_debut, INTERVAL duree MINUTE);


# PASSE à l'état OUV : inscription ouverte

UPDATE sortie
SET etat_id	= (SELECT id FROM etat WHERE code = 'OUV' LIMIT 1)
WHERE date_limite_inscription > NOW();

UPDATE sortie s
    INNER JOIN (
        SELECT sortie_id, COUNT(*) as total
        FROM inscription
        GROUP BY sortie_id
    ) i ON s.id = i.sortie_id
SET s.etat_id = (SELECT id FROM etat WHERE code = 'OUV' LIMIT 1)
WHERE i.total < s.nb_inscriptions_max;

# PASSEE à l'état FIN : Activité terminées
UPDATE sortie
SET etat_id	= (SELECT id FROM etat WHERE code = 'FIN' LIMIT 1)
WHERE NOW() > DATE_ADD(date_heure_debut, INTERVAL duree MINUTE);
