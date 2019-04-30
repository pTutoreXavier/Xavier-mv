-- --------------------------------------------------------
-- Hôte :                        localhost
-- Version du serveur:           5.7.24 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.5.0.5332
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Listage de la structure de la table xavier. xv_commentaire
DROP TABLE IF EXISTS `xv_commentaire`;
CREATE TABLE IF NOT EXISTS `xv_commentaire` (
  `idUser` int(11) NOT NULL,
  `idSequence` int(11) NOT NULL,
  `commentaire` text CHARACTER SET latin1 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Listage de la structure de la table xavier. xv_dictionnaire
DROP TABLE IF EXISTS `xv_dictionnaire`;
CREATE TABLE IF NOT EXISTS `xv_dictionnaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `libelle` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `parametre` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;

-- Listage de la structure de la table xavier. xv_sequence
DROP TABLE IF EXISTS `xv_sequence`;
CREATE TABLE IF NOT EXISTS `xv_sequence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idVideo` int(11) NOT NULL,
  `pseudocode` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `debut` int(11) DEFAULT NULL,
  `fin` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `miniature` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Listage de la structure de la table xavier. xv_user
DROP TABLE IF EXISTS `xv_user`;
CREATE TABLE IF NOT EXISTS `xv_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET latin1 NOT NULL,
  `prenom` varchar(100) CHARACTER SET latin1 NOT NULL,
  `mail` varchar(100) CHARACTER SET latin1 NOT NULL,
  `dateNaissance` date DEFAULT NULL,
  `mdp` varchar(100) CHARACTER SET latin1 NOT NULL,
  `level` int(11) NOT NULL,
  `updated_at` varchar(156) NOT NULL,
  `created_at` varchar(156) NOT NULL,
  `avatar` varchar(156) NOT NULL DEFAULT '0.png',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- Listage des données de la table xavier.xv_user : 3 rows
/*!40000 ALTER TABLE `xv_user` DISABLE KEYS */;
INSERT INTO `xv_user` (`id`, `nom`, `prenom`, `mail`, `dateNaissance`, `mdp`, `level`, `updated_at`, `created_at`, `avatar`) VALUES
	(1, 'Sear', 'Cher', 'cher@cheur.fr', '1987-03-15', '$2y$10$7xn.CZ5THhXYB9ud5dmzGeb0izK6V0z.Oh5ORyMGebtvu0jXD04B2', 779, '2019-03-27 11:22:16', '2019-03-27 11:22:16', '0.png'),
	(2, 'Us', 'Er', 'us@er.fr', '1997-03-15', '$2y$10$0vmJ9qbSZvaKHJvrNj2cdOoT1Kiskgcaf..pT2Sn245N/JAtH0t.O', 500, '2019-03-27 11:23:49', '2019-03-27 11:23:49', '0.png'),
/*!40000 ALTER TABLE `xv_user` ENABLE KEYS */;

-- Listage de la structure de la table xavier. xv_video
DROP TABLE IF EXISTS `xv_video`;
CREATE TABLE IF NOT EXISTS `xv_video` (
  `id` int(11) NOT NULL,
  `lien` text CHARACTER SET latin1 NOT NULL,
  `idChercheur` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
