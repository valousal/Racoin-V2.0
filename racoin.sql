-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 04 Février 2015 à 12:11
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `racoin`
--

-- --------------------------------------------------------

--
-- Structure de la table `annoncesracoin`
--

CREATE TABLE IF NOT EXISTS `annoncesracoin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `tarif` varchar(25) NOT NULL,
  `ville` varchar(250) NOT NULL,
  `CP` varchar(25) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `tel` int(11) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `images` varchar(70) NOT NULL,
  `images_ext` varchar(10) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `password` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Structure de la table `annonceursracoin`
--

CREATE TABLE IF NOT EXISTS `annonceursracoin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `password` varchar(400) NOT NULL,
  `tel` varchar(250) NOT NULL,
  `mail` varchar(250) NOT NULL,
  `ville` varchar(30) NOT NULL,
  `CP` int(11) NOT NULL,
  `id_cat_fav` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `categoriesracoin`
--

CREATE TABLE IF NOT EXISTS `categoriesracoin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `categoriesracoin`
--

INSERT INTO `categoriesracoin` (`id`, `nom`) VALUES
(1, 'Informatique'),
(2, 'Voitures');

-- --------------------------------------------------------

--
-- Structure de la table `client_apiracoin`
--

CREATE TABLE IF NOT EXISTS `client_apiracoin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(40) NOT NULL,
  `token` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Structure de la table `imagesracoin`
--

CREATE TABLE IF NOT EXISTS `imagesracoin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `id_annonce` int(11) NOT NULL,
  `extension` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
