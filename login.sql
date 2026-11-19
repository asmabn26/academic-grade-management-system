-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3308
-- Généré le : jeu. 08 mai 2025 à 17:06
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `login`
--

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `ne` varchar(20) NOT NULL,
  `mot_de_passe` varchar(64) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `nom`, `prenom`, `ne`, `mot_de_passe`, `email`, `date_creation`) VALUES
(1, 'Chbani', 'Iliass', 'NE12345', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'iliass.chbani@example.com', '2025-05-02 14:03:33'),
(2, 'Berrada', 'Mouaad', 'NE67890', 'dd6a12c27b2f141e8db5cf96b2881780304d729b3d18478c6e1226a8a306ac1c', 'mouaad.berrada@example.com', '2025-05-02 14:42:32'),
(3, 'Alaoui', 'Choraih', 'NE24680', '6b1610ef0a5e94b2b786023b96650de87d3c9fe9e89e6c6afda21862ceeca1e3', 'sab.alaoui@example.com', '2025-05-02 14:42:32');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ne` (`ne`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
