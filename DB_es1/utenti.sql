-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 27, 2025 alle 22:03
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `utenti`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti_registrati`
--

CREATE TABLE `utenti_registrati` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti_registrati`
--

INSERT INTO `utenti_registrati` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'gr3g0r', 'gr3g0r@gmail.com', '$2y$10$lD/v8dtauNUblwtyY2VTMuRjn7.1VtBvdcUsrm/uRBQU/gEgjIGBK', '2025-01-09 12:20:48'),
(2, 'marco', 'marco@gmail.com', '$2y$10$90ZHchwZcpAOiClTJlytceUriY6RladUprnc4A9Y/avjM7yPbYFc.', '2025-01-09 12:23:50'),
(3, 'critelli', 'critelli@gmail.com', '$2y$10$7sPacFZOCRJmFhalPCB1BuMlyLUd6VSFwafbRk10EM9vDXEPyFeYO', '2025-01-09 12:45:12'),
(4, 'dajeroma', 'dajeroma@gmail.com', '$2y$10$lBQBNudPBDJsnmLRJz1IqusazH7pOzfMjdG9FTNthHekHWYXXVJgS', '2025-01-27 20:42:12'),
(5, 'daoud', 'daoud@gmail.com', '$2y$10$/PRex.UfOVlu5u7EMKcZGuZmjCgMloH3VIrLGxH4M8a8LjjG2ClB.', '2025-01-27 20:49:32');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `utenti_registrati`
--
ALTER TABLE `utenti_registrati`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `utenti_registrati`
--
ALTER TABLE `utenti_registrati`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
