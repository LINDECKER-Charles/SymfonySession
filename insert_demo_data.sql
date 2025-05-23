-- Catégories
INSERT INTO `category` (`id`, `category_name`) VALUES
(1, 'Bureautique'),
(2, 'Développement Web'),
(3, 'Design'),
(4, 'Marketing');

-- Modules
INSERT INTO `module` (`id`, `module_category_id`, `mudle_name`) VALUES
(1, 1, 'Word'),
(2, 1, 'Excel'),
(3, 2, 'PHP'),
(4, 2, 'JavaScript'),
(5, 3, 'Photoshop'),
(6, 4, 'SEO');

-- Sessions
INSERT INTO `session` (`id`, `session_name`, `start_date`, `end_date`, `nb_place_tt`, `nb_place_reserved`) VALUES
(1, 'Session 1', '2024-04-30 09:00:00', '2024-05-10 17:00:00', 20, 12),
(2, 'Session 2', '2024-05-05 09:00:00', '2024-05-15 17:00:00', 20, 14),
(3, 'Session 3', '2024-05-10 09:00:00', '2024-05-20 17:00:00', 20, 8),
(4, 'Session 4', '2024-05-15 09:00:00', '2024-05-25 17:00:00', 20, 10),
(5, 'Session 5', '2024-05-20 09:00:00', '2024-05-30 17:00:00', 20, 7);

-- Stagiaires
INSERT INTO `intern` (`id`, `inter_name`, `intern_sex`, `intern_city`, `intern_cp`, `intern_adress`, `intern_birth`, `intern_email`) VALUES
(1, 'Virginie Thomas', 'Femme', 'Sainte Virginie', '48140', '104, rue de Collet', '1992-11-22', 'virginie.thomas@example.com');

-- Utilisateurs
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `sex`, `city`, `cp`, `adress`, `birth`) VALUES
(1, 'admin@example.com', '["ROLE_ADMIN"]', 'hashedpassword123', 'Charles Lindecker', 'Homme', 'Strasbourg', '67000', '14 rue du Rhône', '1990-01-01');
