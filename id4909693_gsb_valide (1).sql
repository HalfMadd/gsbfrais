-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  Dim 15 avr. 2018 à 14:57
-- Version du serveur :  10.1.31-MariaDB
-- Version de PHP :  7.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `id4909693_gsb_valide`
--

-- --------------------------------------------------------

--
-- Structure de la table `etat`
--

CREATE TABLE `etat` (
  `id` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('RB', 'Remboursée'),
('VA', 'Validée et mise en paiement');

-- --------------------------------------------------------

--
-- Structure de la table `fichefrais`
--

CREATE TABLE `fichefrais` (
  `idVisiteur` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `mois` char(6) COLLATE utf8_unicode_ci NOT NULL,
  `nbJustificatifs` int(11) DEFAULT NULL,
  `montantValide` decimal(10,2) DEFAULT NULL,
  `dateModif` date DEFAULT NULL,
  `idEtat` char(2) COLLATE utf8_unicode_ci DEFAULT 'CR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `fichefrais`
--

INSERT INTO `fichefrais` (`idVisiteur`, `mois`, `nbJustificatifs`, `montantValide`, `dateModif`, `idEtat`) VALUES
('a131', '201802', 0, NULL, '2018-03-07', 'CR'),
('b34', '201705', 0, NULL, '2017-05-12', 'CR'),
('c3', '201705', 0, NULL, '2018-02-16', 'RB'),
('c3', '201706', 0, NULL, '2017-09-10', 'CL'),
('c3', '201709', 0, NULL, '2018-02-28', 'RB'),
('c3', '201710', 0, NULL, '2017-11-12', 'CL'),
('c3', '201711', 0, NULL, '2017-12-07', 'CL'),
('c3', '201712', 0, NULL, '2018-01-22', 'CL'),
('c3', '201801', 0, NULL, '2018-02-05', 'CL'),
('c3', '201802', 0, NULL, '2018-03-05', 'CL'),
('c3', '201803', 0, NULL, '2018-03-05', 'CR'),
('f39', '201802', 0, NULL, '2018-02-16', 'CR'),
('f4', '201802', 0, NULL, '2018-02-16', 'CR');

-- --------------------------------------------------------

--
-- Structure de la table `fraisforfait`
--

CREATE TABLE `fraisforfait` (
  `id` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `fraisforfait`
--

INSERT INTO `fraisforfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', 110.00),
('KM', 'Frais Kilométrique', 0.62),
('NUI', 'Nuitée Hôtel', 80.00),
('REP', 'Repas Restaurant', 25.00);

-- --------------------------------------------------------

--
-- Structure de la table `lignefraisforfait`
--

CREATE TABLE `lignefraisforfait` (
  `idVisiteur` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `mois` char(6) COLLATE utf8_unicode_ci NOT NULL,
  `idFraisForfait` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `quantite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `lignefraisforfait`
--

INSERT INTO `lignefraisforfait` (`idVisiteur`, `mois`, `idFraisForfait`, `quantite`) VALUES
('a131', '201802', 'ETP', 0),
('a131', '201802', 'KM', 0),
('a131', '201802', 'NUI', 0),
('a131', '201802', 'REP', 0),
('b34', '201705', 'ETP', 0),
('b34', '201705', 'KM', 0),
('b34', '201705', 'NUI', 0),
('b34', '201705', 'REP', 0),
('c3', '201705', 'ETP', 1),
('c3', '201705', 'KM', 1),
('c3', '201705', 'NUI', 1),
('c3', '201705', 'REP', 1),
('c3', '201706', 'ETP', 0),
('c3', '201706', 'KM', 0),
('c3', '201706', 'NUI', 0),
('c3', '201706', 'REP', 0),
('c3', '201709', 'ETP', 0),
('c3', '201709', 'KM', 5),
('c3', '201709', 'NUI', 0),
('c3', '201709', 'REP', 0),
('c3', '201710', 'ETP', 20),
('c3', '201710', 'KM', 15),
('c3', '201710', 'NUI', 8),
('c3', '201710', 'REP', 10),
('c3', '201711', 'ETP', 0),
('c3', '201711', 'KM', 0),
('c3', '201711', 'NUI', 0),
('c3', '201711', 'REP', 0),
('c3', '201712', 'ETP', 10),
('c3', '201712', 'KM', 10),
('c3', '201712', 'NUI', 0),
('c3', '201712', 'REP', 0),
('c3', '201801', 'ETP', 5),
('c3', '201801', 'KM', 2),
('c3', '201801', 'NUI', 3),
('c3', '201801', 'REP', 9),
('c3', '201802', 'ETP', 20),
('c3', '201802', 'KM', 20),
('c3', '201802', 'NUI', 20),
('c3', '201802', 'REP', 10),
('c3', '201803', 'ETP', 100),
('c3', '201803', 'KM', 0),
('c3', '201803', 'NUI', 0),
('c3', '201803', 'REP', 0),
('f39', '201802', 'ETP', 0),
('f39', '201802', 'KM', 0),
('f39', '201802', 'NUI', 0),
('f39', '201802', 'REP', 0),
('f4', '201802', 'ETP', 1),
('f4', '201802', 'KM', 1),
('f4', '201802', 'NUI', 1),
('f4', '201802', 'REP', 1);

-- --------------------------------------------------------

--
-- Structure de la table `lignefraishorsforfait`
--

CREATE TABLE `lignefraishorsforfait` (
  `id` int(11) NOT NULL,
  `idVisiteur` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `mois` char(6) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `lignefraishorsforfait`
--

INSERT INTO `lignefraishorsforfait` (`id`, `idVisiteur`, `mois`, `libelle`, `date`, `montant`) VALUES
(1, 'c3', '201709', 'REFUSE:un truc', '2017-09-10', 10.00),
(2, 'c3', '201709', 'REFUSE: un truc     ', '2017-09-10', 10.00),
(5, 'c3', '201710', 'REFUSE:anniverssaire', '2017-09-29', 18.00),
(6, 'c3', '201710', 'REFUSE: REFUSE:une cuite       ', '2017-10-22', 150.00),
(8, 'c3', '201712', 'REFUSE:REFUSE:Nouveau test', '2017-12-12', 12.00),
(9, 'c3', '201712', 'REFUSE:REFUSE:un autre test', '2017-12-12', 21.00),
(18, 'c3', '201712', 'REFUSE:qmkeodgsf', '2017-12-12', 1212.00),
(19, 'c3', '201712', 'REFUSE:qmkeodgsf', '2017-12-12', 1212.00),
(20, 'c3', '201712', 'REFUSE:nli,;', '2017-12-12', 12.00),
(21, 'c3', '201712', 'REFUSE:nli,;', '2017-12-12', 12.00),
(25, 'c3', '201802', 'REFUSE: erhtgbfre\"rthg ', '2018-02-14', 150.00),
(26, 'c3', '201802', 'REFUSE: erhtgbfre\"rth  ', '2018-02-14', 150.00),
(27, 'c3', '201802', 'REFUSE: erhtgbfre\"rt  ', '2018-02-14', 150.00),
(28, 'c3', '201802', 'REFUSE: erhtgbfre\"          ', '2018-02-14', 150.00),
(29, 'c3', '201802', 'REFUSE: zervdsqze  ', '2018-02-15', 123.00),
(31, 'c3', '201802', ',ezfiosfjdx', '2018-02-28', 160.00),
(32, 'c3', '201803', 'test entre 1 et 10 mois', '2018-03-05', 134.00),
(33, 'c3', '201802', 'test mois', '2018-03-05', 135.00);

-- --------------------------------------------------------

--
-- Structure de la table `visiteur`
--

CREATE TABLE `visiteur` (
  `id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `nom` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mdp` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `visiteur`
--

INSERT INTO `visiteur` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateEmbauche`, `admin`) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 0),
('a17', 'Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 0),
('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 0),
('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 0),
('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 0),
('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 0),
('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 0),
('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05', 0),
('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 0),
('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la République', '75011', 'Paris', '2008-09-23', 0),
('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12', 0),
('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 0),
('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 0),
('c14', 'Daburon', 'François', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11', 0),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Créteil', '2010-12-14', 0),
('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 0),
('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 0),
('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 0),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orléans', '2005-11-12', 0),
('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05', 0),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', '44im8', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01', 0),
('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 0),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 0),
('e52', 'Eynde', 'Valérie', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 0),
('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 0),
('f39', 'Frémont', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 0),
('f4', 'Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 0),
('h21', 'LEBO', 'Roger', 'rle', 'd3f3ns3', NULL, NULL, NULL, NULL, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `etat`
--
ALTER TABLE `etat`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fichefrais`
--
ALTER TABLE `fichefrais`
  ADD PRIMARY KEY (`idVisiteur`,`mois`),
  ADD KEY `idEtat` (`idEtat`);

--
-- Index pour la table `fraisforfait`
--
ALTER TABLE `fraisforfait`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `lignefraisforfait`
--
ALTER TABLE `lignefraisforfait`
  ADD PRIMARY KEY (`idVisiteur`,`mois`,`idFraisForfait`),
  ADD KEY `idFraisForfait` (`idFraisForfait`);

--
-- Index pour la table `lignefraishorsforfait`
--
ALTER TABLE `lignefraishorsforfait`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idVisiteur` (`idVisiteur`,`mois`);

--
-- Index pour la table `visiteur`
--
ALTER TABLE `visiteur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lignefraishorsforfait`
--
ALTER TABLE `lignefraishorsforfait`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fichefrais`
--
ALTER TABLE `fichefrais`
  ADD CONSTRAINT `fichefrais_ibfk_1` FOREIGN KEY (`idEtat`) REFERENCES `etat` (`id`),
  ADD CONSTRAINT `fichefrais_ibfk_2` FOREIGN KEY (`idVisiteur`) REFERENCES `visiteur` (`id`);

--
-- Contraintes pour la table `lignefraisforfait`
--
ALTER TABLE `lignefraisforfait`
  ADD CONSTRAINT `lignefraisforfait_ibfk_1` FOREIGN KEY (`idVisiteur`,`mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`),
  ADD CONSTRAINT `lignefraisforfait_ibfk_2` FOREIGN KEY (`idFraisForfait`) REFERENCES `fraisforfait` (`id`);

--
-- Contraintes pour la table `lignefraishorsforfait`
--
ALTER TABLE `lignefraishorsforfait`
  ADD CONSTRAINT `lignefraishorsforfait_ibfk_1` FOREIGN KEY (`idVisiteur`,`mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
