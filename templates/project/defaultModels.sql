-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 07 juil. 2023 à 00:55
-- Version du serveur : 8.0.33-0ubuntu0.20.04.2
-- Version de PHP : 8.0.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `guideme_decathlon`
--

-- --------------------------------------------------------

--
-- Structure de la table `LangMessage`
--

CREATE TABLE `LangMessage` (
  `id` int NOT NULL,
  `lang_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `LangType`
--

CREATE TABLE `LangType` (
  `id` int NOT NULL,
  `name` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `LangType`
--

INSERT INTO `LangType` (`id`, `name`) VALUES
(1, 'fr'),
(2, 'en');

-- --------------------------------------------------------

--
-- Structure de la table `PermissionType`
--

CREATE TABLE `PermissionType` (
  `id` int NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `PermissionType`
--

INSERT INTO `PermissionType` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user'),
(3, 'anonymous');

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE `User` (
  `id` int NOT NULL,
  `email` varchar(64) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Index pour la table `LangMessage`
--
ALTER TABLE `LangMessage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lang_id_2` (`lang_id`,`name`),
  ADD KEY `lang_id` (`lang_id`);

--
-- Index pour la table `LangType`
--
ALTER TABLE `LangType`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `PermissionType`
--
ALTER TABLE `PermissionType`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `permission_id` (`permission_id`);

--
-- AUTO_INCREMENT pour la table `LangMessage`
--
ALTER TABLE `LangMessage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `LangType`
--
ALTER TABLE `LangType`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `PermissionType`
--
ALTER TABLE `PermissionType`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `User`
--
ALTER TABLE `User`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `LangMessage`
--
ALTER TABLE `LangMessage`
  ADD CONSTRAINT `lmid` FOREIGN KEY (`lang_id`) REFERENCES `LangType` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `PermissionType` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
