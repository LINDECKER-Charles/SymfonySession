-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour session-symfony
CREATE DATABASE IF NOT EXISTS `session-symfony` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `session-symfony`;

-- Listage de la structure de table session-symfony. category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.category : ~4 rows (environ)
INSERT INTO `category` (`id`, `category_name`) VALUES
	(1, 'Bureautique'),
	(2, 'Développement Web'),
	(3, 'Design'),
	(4, 'Marketing'),
	(101, 'Virtualisation'),
	(102, 'Sécurité Réseau');

-- Listage de la structure de table session-symfony. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Listage des données de la table session-symfony.doctrine_migration_versions : ~1 rows (environ)
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20250523133630', '2025-05-23 13:37:07', 155);

-- Listage de la structure de table session-symfony. intern
CREATE TABLE IF NOT EXISTS `intern` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inter_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intern_sex` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intern_city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intern_cp` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intern_adress` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intern_birth` date NOT NULL,
  `intern_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.intern : ~2 rows (environ)
INSERT INTO `intern` (`id`, `inter_name`, `intern_sex`, `intern_city`, `intern_cp`, `intern_adress`, `intern_birth`, `intern_email`) VALUES
	(1, 'Virginie Thomas', 'Femme', 'Sainte Virginie', '48140', '104, rue de Collet', '1992-11-22', 'virginie.thomas@example.com'),
	(2, 'Rémy Lemoine-Blin', 'Femme', 'Hamel', '42353', '4, rue Evrard', '1998-09-21', 'daniellelefebvre@noos.fr');

-- Listage de la structure de table session-symfony. intern_session
CREATE TABLE IF NOT EXISTS `intern_session` (
  `intern_id` int NOT NULL,
  `session_id` int NOT NULL,
  PRIMARY KEY (`intern_id`,`session_id`),
  KEY `IDX_A6D9BBE2525DD4B4` (`intern_id`),
  KEY `IDX_A6D9BBE2613FECDF` (`session_id`),
  CONSTRAINT `FK_A6D9BBE2525DD4B4` FOREIGN KEY (`intern_id`) REFERENCES `intern` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A6D9BBE2613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.intern_session : ~0 rows (environ)

-- Listage de la structure de table session-symfony. messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.messenger_messages : ~0 rows (environ)

-- Listage de la structure de table session-symfony. module
CREATE TABLE IF NOT EXISTS `module` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_category_id` int NOT NULL,
  `mudle_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2426289C6D9730` (`module_category_id`),
  CONSTRAINT `FK_C2426289C6D9730` FOREIGN KEY (`module_category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.module : ~6 rows (environ)
INSERT INTO `module` (`id`, `module_category_id`, `mudle_name`) VALUES
	(1, 1, 'Word'),
	(2, 1, 'Excel'),
	(3, 2, 'PHP'),
	(4, 2, 'JavaScript'),
	(5, 3, 'Photoshop'),
	(6, 4, 'SEO'),
	(201, 101, 'VMware & Hyper-V'),
	(202, 102, 'Firewall & VPN');

-- Listage de la structure de table session-symfony. programme
CREATE TABLE IF NOT EXISTS `programme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `module_id` int NOT NULL,
  `nb_day` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3DDCB9FF613FECDF` (`session_id`),
  KEY `IDX_3DDCB9FFAFC2B591` (`module_id`),
  CONSTRAINT `FK_3DDCB9FF613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`),
  CONSTRAINT `FK_3DDCB9FFAFC2B591` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.programme : ~0 rows (environ)

-- Listage de la structure de table session-symfony. session
CREATE TABLE IF NOT EXISTS `session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `nb_place_tt` int NOT NULL,
  `nb_place_reserved` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.session : ~5 rows (environ)
INSERT INTO `session` (`id`, `session_name`, `start_date`, `end_date`, `nb_place_tt`, `nb_place_reserved`) VALUES
	(1, 'Session 1', '2024-04-30 09:00:00', '2024-05-10 17:00:00', 20, 12),
	(2, 'Session 2', '2024-05-05 09:00:00', '2024-05-15 17:00:00', 20, 14),
	(3, 'Session 3', '2024-05-10 09:00:00', '2024-05-20 17:00:00', 20, 8),
	(4, 'Session 4', '2024-05-15 09:00:00', '2024-05-25 17:00:00', 20, 10),
	(5, 'Session 5', '2024-05-20 09:00:00', '2024-05-30 17:00:00', 20, 7),
	(100, 'Session Full Stack Web', '2024-06-01 00:00:00', '2024-07-15 00:00:00', 10, 4),
	(301, 'Session Virtualisation - Mars 2024', '2024-03-01 00:00:00', '2024-03-10 00:00:00', 20, 18),
	(302, 'Session Sécurité - Avril 2024', '2024-04-05 00:00:00', '2024-04-15 00:00:00', 15, 12);

-- Listage de la structure de table session-symfony. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cp` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adress` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.user : ~2 rows (environ)
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `sex`, `city`, `cp`, `adress`, `birth`) VALUES
	(2, 'charles.lindecker@outlook.fr', '["ROLE_ADMIN"]', '$2y$13$2sf12r2fmKu7YzMTsq0H6uOTrl1AvYSBjTO/GgVk.tPHKhR1LJisq', 'LINDECKER Charles', 'Homme', 'BAAHAHA', '68720', 'BAAHH65', '2003-07-28'),
	(3, 'jean.edouard@ratio.gg', '[]', '$2y$13$UljnNf1Yr6v3HkGt4zxoxuziE6rAw4wa.RyFBXdNzxDKhtsiT0Z0O', 'Jean EDOUARD', 'Homme', 'aezaezaez', 'aezaezaez', 'aezaezaez', '0001-12-06');

-- Listage de la structure de table session-symfony. user_session
CREATE TABLE IF NOT EXISTS `user_session` (
  `user_id` int NOT NULL,
  `session_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`session_id`),
  KEY `IDX_8849CBDEA76ED395` (`user_id`),
  KEY `IDX_8849CBDE613FECDF` (`session_id`),
  CONSTRAINT `FK_8849CBDE613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8849CBDEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table session-symfony.user_session : ~0 rows (environ)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
