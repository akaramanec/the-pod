-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Час створення: Січ 18 2025 р., 21:26
-- Версія сервера: 5.7.39
-- Версія PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `the-pod-log`
--

-- --------------------------------------------------------

--
-- Структура таблиці `auth_logger`
--

CREATE TABLE `auth_logger` (
  `id` int(10) NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `request` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп даних таблиці `auth_logger`
--

INSERT INTO `auth_logger` (`id`, `admin_id`, `controller`, `action`, `data`, `request`, `created_at`, `updated_at`) VALUES
(1, 13, 'order', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:16:38', NULL),
(2, 13, 'order', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:40:51', NULL),
(3, 13, 'order', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:41:08', NULL),
(4, 13, 'customer-blogger', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:44:13', NULL),
(5, 13, 'customer', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:44:16', NULL),
(6, 13, 'order', 'index', '[]', '{\"get\": [], \"post\": []}', '2024-11-21 23:44:18', NULL);

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `auth_logger`
--
ALTER TABLE `auth_logger`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `auth_logger`
--
ALTER TABLE `auth_logger`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
