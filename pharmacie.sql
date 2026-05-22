-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 10 Juin 2025 à 21:08
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `pharmacie`
--

-- --------------------------------------------------------

--
-- Structure de la table `en_attente`
--

CREATE TABLE IF NOT EXISTS `en_attente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_facture` varchar(13) NOT NULL,
  `nom_medicament` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `date_expiration` date NOT NULL,
  `quantite` bigint(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `cout` bigint(11) NOT NULL,
  `montant` bigint(11) NOT NULL,
  `montant_profit` bigint(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

--
-- Contenu de la table `en_attente`
--

INSERT INTO `en_attente` (`id`, `numero_facture`, `nom_medicament`, `categorie`, `date_expiration`, `quantite`, `type`, `cout`, `montant`, `montant_profit`, `date`) VALUES
(1, 'RS-9390009', 'Biogessic', 'Antidouleur', '2020-03-31', 1, 'Stp', 500, 500, 100, '22/02/2019'),
(63, 'RS-2909290', 'Paracétamol', 'Antidouleur', '2019-10-01', 10, 'Bot', 500, 5000, 1000, '05/03/2019');

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `code_barres` varchar(255) NOT NULL,
  `nom_medicament` varchar(100) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `quantite` int(100) NOT NULL,
  `quantite_utilisee` int(100) NOT NULL,
  `quantite_restante` int(100) NOT NULL,
  `quantite_reelle` int(10) NOT NULL,
  `date_enregistrement` date NOT NULL,
  `date_expiration` date NOT NULL,
  `compagnie` varchar(100) NOT NULL,
  `type_vente` varchar(100) NOT NULL,
  `prix_achat` int(100) NOT NULL,
  `prix_vente` int(100) NOT NULL,
  `prix_profit` varchar(100) NOT NULL,
  `statut` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Contenu de la table `stock`
--

INSERT INTO `stock` (`id`, `code_barres`, `nom_medicament`, `categorie`, `quantite`, `quantite_utilisee`, `quantite_restante`, `quantite_reelle`, `date_enregistrement`, `date_expiration`, `compagnie`, `type_vente`, `prix_achat`, `prix_vente`, `prix_profit`, `statut`) VALUES
(21, '8901138821852', 'Paracétamol', 'Antidouleur', 20, 18, 2, 12, '2019-03-04', '2019-10-01', '', 'Bot', 400, 500, '100(25%)', 'Disponible'),
(23, '071661013678', 'Biogessic', 'Antidouleur', 50, 4, 46, 50, '2019-03-05', '2020-03-06', '', 'Bot', 500, 700, '200(40%)', 'Disponible');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(10) NOT NULL,
  `mot_de_passe` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom_utilisateur`, `mot_de_passe`) VALUES
(0, 'admin', 'admin'),
(1, 'quelquun', '123'),
(2, 'admin', 'admin'),
(3, 'JEAN ', '123');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE IF NOT EXISTS `ventes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_facture` varchar(13) NOT NULL,
  `medicaments` varchar(255) NOT NULL,
  `quantite` varchar(255) NOT NULL,
  `montant_total` bigint(11) NOT NULL,
  `profit_total` bigint(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `ventes`
--

INSERT INTO `ventes` (`id`, `numero_facture`, `medicaments`, `quantite`, `montant_total`, `profit_total`, `date`) VALUES
(1, 'RS-9390009', 'Biogessic', '1(Stp)', 500, 100, '2019-02-22'),
(15, 'RS-0000032', 'Paracétamol,Biogessic', '5(Bot),4(Bot)', 5300, 1300, '2019-03-05');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
