-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 20 déc. 2023 à 16:54
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `br`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `task_id` int(11) UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `posted_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `has_replies` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `task_id`, `comment_text`, `posted_by`, `created_at`, `has_replies`) VALUES
(4, 80, 'niice', 9, '2023-12-20 14:40:42', 0),
(5, 80, 'goood', 9, '2023-12-20 16:38:40', 0),
(6, 73, 'nadi', 9, '2023-12-20 16:47:23', 0),
(7, 73, 'woow', 6, '2023-12-20 16:48:00', 0),
(8, 82, 'wooow', 6, '2023-12-20 16:52:05', 0);

-- --------------------------------------------------------

--
-- Structure de la table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `id` int(11) UNSIGNED NOT NULL,
  `comment_id` int(11) UNSIGNED NOT NULL,
  `reply_text` text NOT NULL,
  `replied_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comment_replies`
--

INSERT INTO `comment_replies` (`id`, `comment_id`, `reply_text`, `replied_by`, `created_at`) VALUES
(5, 4, 'nadi', 9, '2023-12-20 16:33:34'),
(6, 6, 'kml', 9, '2023-12-20 16:47:27'),
(7, 6, 'yeah', 6, '2023-12-20 16:47:49'),
(8, 8, 'yeah', 9, '2023-12-20 16:52:29');

-- --------------------------------------------------------

--
-- Structure de la table `developers`
--

CREATE TABLE `developers` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `developers`
--

INSERT INTO `developers` (`id`, `name`, `email`, `username`, `password`) VALUES
(1, 'Naoufal', 'developer@example.com', 'sampledeveloper', 'hashedpassword'),
(2, 'Walid', '', '', ''),
(3, 'Mohsine', '', '', ''),
(4, 'Ilyass', '', '', ''),
(5, 'Hamza', '', '', ''),
(6, 'don', 'don@gmail.com', 'don', '$2y$10$LVlm0bhMDwpkbQEe3Tup/.O6dHcvVeqiRlNUhVXSRQnWle5JJnr3W'),
(8, 'AnotherDeveloper', '', '', ''),
(9, 'nano', 'nano@gmail.com', 'nano', '$2y$10$JRWUXEt9qfG72c6Eo2kklukX2pM/W3vxGu/3nfTXs6q94R.bjolbi');

-- --------------------------------------------------------

--
-- Structure de la table `priorities`
--

CREATE TABLE `priorities` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `priorities`
--

INSERT INTO `priorities` (`id`, `name`) VALUES
(1, 'High'),
(2, 'Medium'),
(3, 'Low');

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'Feature'),
(2, 'Bug'),
(3, 'Enhancement');

-- --------------------------------------------------------

--
-- Structure de la table `todo`
--

CREATE TABLE `todo` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('todo','doing','done') NOT NULL DEFAULT 'todo',
  `priority_id` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `todo`
--

INSERT INTO `todo` (`id`, `title`, `description`, `created_at`, `status`, `priority_id`, `created_by`) VALUES
(56, 'dash', NULL, '2023-12-18 22:33:26', 'done', 1, 9),
(57, 'frontend', NULL, '2023-12-18 22:39:04', 'doing', 1, 9),
(58, 'back', NULL, '2023-12-18 22:39:22', 'doing', 1, 9),
(62, 'sos', NULL, '2023-12-19 10:26:40', 'done', 1, 6),
(67, 'test ', NULL, '2023-12-19 15:21:07', 'done', 2, 6),
(68, 'test1', NULL, '2023-12-19 15:24:30', 'done', 1, 6),
(69, 'test2', NULL, '2023-12-19 15:24:40', 'doing', 2, 6),
(70, 'test 3 ', NULL, '2023-12-19 15:24:47', 'doing', 1, 6),
(72, 'task', NULL, '2023-12-19 16:26:59', 'doing', 2, 9),
(73, 'qqqq', NULL, '2023-12-19 16:36:33', 'todo', 1, 9),
(74, 'wkz', NULL, '2023-12-19 16:36:40', 'done', 1, 9),
(75, 'sjq', NULL, '2023-12-19 16:57:45', 'doing', 1, 6),
(76, 'testttttiii', NULL, '2023-12-19 17:00:45', 'doing', 2, 9),
(77, 'qqqqhbnz', NULL, '2023-12-19 17:52:23', 'done', 2, 6),
(78, 'tas', 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.', '2023-12-19 21:43:24', 'doing', 1, 9),
(80, 'jqbh', 'On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même. L\'avantage du Lorem Ipsum sur un texte générique comme \'Du texte. Du texte. Du texte.\' est qu\'il possède une distribution de lettres plus ou moins normale, et en tout cas comparable avec celle du français standard. De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut, et une recherche pour \'Lorem Ipsum\' vous conduira vers de nombreux sites qui n\'en sont encore qu\'à leur phase de construction. Plusieurs versions sont apparues avec le temps, parfois par accident, souvent intentionnellement (histoire d\'y rajouter de petits clins d\'oeil, voire des phrases em', '2023-12-20 10:26:15', 'todo', 1, 9),
(82, 'Lorem Ipsum', 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.', '2023-12-20 16:51:50', 'todo', 1, 6);

-- --------------------------------------------------------

--
-- Structure de la table `todo_developers`
--

CREATE TABLE `todo_developers` (
  `id` int(11) UNSIGNED NOT NULL,
  `todo_id` int(11) UNSIGNED NOT NULL,
  `developer_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `todo_developers`
--

INSERT INTO `todo_developers` (`id`, `todo_id`, `developer_id`) VALUES
(71, 56, 2),
(72, 56, 4),
(73, 57, 2),
(74, 57, 4),
(75, 58, 1),
(76, 58, 2),
(83, 62, 3),
(84, 62, 4),
(93, 67, 2),
(94, 67, 3),
(95, 68, 2),
(96, 68, 3),
(97, 69, 2),
(98, 69, 3),
(99, 69, 4),
(100, 70, 2),
(101, 70, 3),
(104, 72, 5),
(105, 72, 6),
(106, 72, 9),
(107, 73, 3),
(108, 73, 6),
(109, 73, 8),
(110, 73, 9),
(111, 74, 9),
(112, 75, 2),
(113, 75, 6),
(114, 76, 1),
(115, 76, 6),
(116, 77, 2),
(117, 77, 5),
(118, 77, 9),
(119, 78, 2),
(120, 78, 3),
(121, 78, 9),
(125, 80, 2),
(126, 80, 3),
(130, 82, 3),
(131, 82, 4),
(132, 82, 9);

-- --------------------------------------------------------

--
-- Structure de la table `todo_tags`
--

CREATE TABLE `todo_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `todo_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `todo_tags`
--

INSERT INTO `todo_tags` (`id`, `todo_id`, `tag_id`) VALUES
(65, 56, 1),
(66, 56, 2),
(67, 56, 3),
(68, 57, 1),
(69, 58, 1),
(70, 58, 3),
(74, 62, 1),
(82, 67, 1),
(83, 68, 1),
(84, 69, 2),
(85, 69, 3),
(86, 70, 2),
(88, 72, 1),
(89, 73, 1),
(90, 74, 1),
(91, 75, 1),
(92, 76, 2),
(93, 77, 1),
(94, 78, 2),
(95, 78, 3),
(97, 80, 1),
(100, 82, 1),
(101, 82, 3);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_task_id` (`task_id`),
  ADD KEY `fk_comments_posted_by` (`posted_by`);

--
-- Index pour la table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `replied_by` (`replied_by`);

--
-- Index pour la table `developers`
--
ALTER TABLE `developers`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `priorities`
--
ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `priority_id` (`priority_id`),
  ADD KEY `fk_todo_created_by` (`created_by`);

--
-- Index pour la table `todo_developers`
--
ALTER TABLE `todo_developers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_todo_id` (`todo_id`),
  ADD KEY `idx_developer_id` (`developer_id`);

--
-- Index pour la table `todo_tags`
--
ALTER TABLE `todo_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_todo_tag` (`todo_id`,`tag_id`),
  ADD KEY `fk_todo_tags_tag_id` (`tag_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `developers`
--
ALTER TABLE `developers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `priorities`
--
ALTER TABLE `priorities`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `todo`
--
ALTER TABLE `todo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT pour la table `todo_developers`
--
ALTER TABLE `todo_developers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT pour la table `todo_tags`
--
ALTER TABLE `todo_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `developers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_task_id` FOREIGN KEY (`task_id`) REFERENCES `todo` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD CONSTRAINT `comment_replies_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_replies_ibfk_2` FOREIGN KEY (`replied_by`) REFERENCES `developers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `todo`
--
ALTER TABLE `todo`
  ADD CONSTRAINT `fk_todo_created_by` FOREIGN KEY (`created_by`) REFERENCES `developers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_todo_priority_id` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `todo_developers`
--
ALTER TABLE `todo_developers`
  ADD CONSTRAINT `fk_todo_dev_developer_id` FOREIGN KEY (`developer_id`) REFERENCES `developers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_todo_dev_todo_id` FOREIGN KEY (`todo_id`) REFERENCES `todo` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `todo_tags`
--
ALTER TABLE `todo_tags`
  ADD CONSTRAINT `fk_todo_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_todo_tags_todo_id` FOREIGN KEY (`todo_id`) REFERENCES `todo` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
