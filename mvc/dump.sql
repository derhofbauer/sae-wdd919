-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Erstellungszeit: 19. Nov 2020 um 19:30
-- Server-Version: 10.4.12-MariaDB-1:10.4.12+maria~bionic
-- PHP-Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mvc`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `street_nr` varchar(255) NOT NULL,
  `extra` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `country`, `city`, `zip`, `street`, `street_nr`, `extra`) VALUES
(1, 1, 'AT', 'Vienna', '1010', 'Hohenstauffengasse', '6', NULL),
(2, 2, 'AT', 'Vienna', '1010', 'Hohenstauffengasse', '6', NULL),
(3, 1, 'at', 'Vienna', '1010', 'Hohenstaufengasse', '8', ''),
(4, 1, 'at', 'Vienna', '1010', 'Hohenstaufengasse', '6', 'Whole Building');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Cool stuff'),
(2, 'Ok stuff'),
(3, 'Boring stuff');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `crdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp() COMMENT 'creation date',
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `products` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Serialized JSON of ordered products',
  `status` enum('open','in progress','in delivery','storno','delivered') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `crdate`, `user_id`, `address_id`, `payment_id`, `products`, `status`) VALUES
(1, '2020-11-12 19:30:09', 1, 4, 2, '[{\"id\":1,\"name\":\"Product 1\",\"description\":\"Product 2 Description\",\"price\":42.99,\"stock\":10,\"images\":\"1603997590_pimp-rollator.jpg\",\"quantity\":\"5\",\"subtotal\":214.95000000000002}]', 'open'),
(2, '2020-11-12 19:29:54', 1, 1, 2, '[{\"id\":1,\"name\":\"Product 1\",\"description\":\"Product 2 Description\",\"price\":42.99,\"stock\":10,\"images\":\"1603997590_pimp-rollator.jpg\",\"quantity\":\"5\",\"subtotal\":214.95000000000002}]', 'in delivery'),
(3, '2020-11-12 19:30:03', 1, 1, 2, '[{\"id\":1,\"name\":\"Product 1\",\"description\":\"Product 2 Description\",\"price\":42.99,\"stock\":10,\"images\":\"1603997590_pimp-rollator.jpg\",\"quantity\":\"5\"}]', 'open');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `expires` varchar(255) NOT NULL,
  `ccv` varchar(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `payments`
--

INSERT INTO `payments` (`id`, `name`, `number`, `expires`, `ccv`, `user_id`) VALUES
(1, 'Max Mustermann', '123456789', '12-2022', '0123', 2),
(2, 'Arthur Dent Jr', '987654321', '12-2022', '4321', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(280) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float NOT NULL,
  `stock` int(11) NOT NULL,
  `images` text COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `images`) VALUES
(1, 'Product 1', 'Product 2 Description', 42.99, 10, '1603997590_pimp-rollator.jpg'),
(2, 'Product 2', 'Product 2 Description', 42, 15, '1604432186_37844315_454803461597516_8815318794768482304_n (1).jpg'),
(3, 'Product 3', 'Product 3 Description', 41.99, 29, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products_categories_mm`
--

CREATE TABLE `products_categories_mm` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `products_categories_mm`
--

INSERT INTO `products_categories_mm` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 2),
(4, 3, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `username`, `firstname`, `lastname`, `is_admin`, `deleted_at`) VALUES
(1, 'admin@shop.com', '$2y$12$mONZuM2SJSo425/axQfqcOBPYfFew8TXDAKt9rwGjWQLOlO8xAkwi', 'admin', 'User', 'One', 1, NULL),
(2, 'user@shop.com', '$2y$12$.P6HA4LEjI7qT5jBTBlB5uY1UgLThZkYS6KDKe2BdgqoDFZvrOanq', 'user', 'User', 'Two (regular User)', NULL, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `products_categories_mm`
--
ALTER TABLE `products_categories_mm`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `products_categories_mm`
--
ALTER TABLE `products_categories_mm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
