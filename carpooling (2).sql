-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 08, 2025 alle 13:34
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
-- Database: `carpooling`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `autisti`
--

CREATE TABLE `autisti` (
  `id_autista` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `numero_patente` varchar(50) NOT NULL,
  `scadenza_patente` date NOT NULL,
  `veicolo` varchar(100) NOT NULL,
  `targa_veicolo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `autisti`
--

INSERT INTO `autisti` (`id_autista`, `id_utente`, `numero_patente`, `scadenza_patente`, `veicolo`, `targa_veicolo`) VALUES
(1, 1, '37yr0eifw98hf', '2035-06-04', 'cabriolet', '398yr19h'),
(3, 5, '93foeibf48fb', '2035-11-14', 'bugatti', '203hf200eif');

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazioni`
--

CREATE TABLE `prenotazioni` (
  `id_prenotazione` int(11) NOT NULL,
  `id_viaggio` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `stato` enum('aperto','chiuso') NOT NULL DEFAULT 'aperto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prenotazioni`
--

INSERT INTO `prenotazioni` (`id_prenotazione`, `id_viaggio`, `id_utente`, `stato`) VALUES
(4, 6, 3, 'aperto');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id_utente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `data_nascita` date NOT NULL,
  `documento` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `ruolo` enum('autista','utente') NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id_utente`, `nome`, `cognome`, `data_nascita`, `documento`, `email`, `telefono`, `ruolo`, `password`) VALUES
(1, 'gabriele', 'llerena', '2006-06-04', 'y0wiehpwh', 'gabriele.llerena@gmail.com', '342818155', 'autista', '$2y$10$4Li3OZRsPJ/qkbeTijkGmegcDfsa2WBDt2yqY0VPZHXp4jJeVtOcS'),
(3, 'gianni', 'llerena', '2025-02-05', '340ng0nwr', 'gianni@gmail.com', '30235023', '', '$2y$10$uPYMV/5s1sfd9U5ub70jaO6DMm5NGyU.0.gLaZgGk/KnpsdoD1QTy'),
(4, 'marco', 'daoud', '2012-10-18', '94u029t', 'daoud@gmail.com', '305656565', '', '$2y$10$aJXVP6q/MjWDcu3ztDu7m.qB61IpBNgJQhxqI1G9ExhKi8uu7s3kK'),
(5, 'matteo', 'critelli', '2002-06-04', '300fei0wefh', 'critelli@gmail.com', '4650732098', 'autista', '$2y$10$deBcI6r9T.Nl7HN57NGBVOj01zM6LnXKE7SHn9ULzI2Ab0f0LM9tu');

-- --------------------------------------------------------

--
-- Struttura della tabella `viaggi`
--

CREATE TABLE `viaggi` (
  `id_viaggio` int(11) NOT NULL,
  `id_autista` int(11) NOT NULL,
  `citta_partenza` varchar(50) NOT NULL,
  `citta_destinazione` varchar(50) NOT NULL,
  `data_partenza` date NOT NULL,
  `ora_partenza` time NOT NULL,
  `contributo_economico` decimal(5,2) NOT NULL,
  `tempo_stimato` varchar(50) NOT NULL,
  `posti_disponibili` int(11) NOT NULL,
  `stato` enum('aperto','chiuso') NOT NULL DEFAULT 'aperto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `viaggi`
--

INSERT INTO `viaggi` (`id_viaggio`, `id_autista`, `citta_partenza`, `citta_destinazione`, `data_partenza`, `ora_partenza`, `contributo_economico`, `tempo_stimato`, `posti_disponibili`, `stato`) VALUES
(1, 1, 'Milano', 'Roma', '2025-04-30', '10:00:00', 130.00, '6', 3, 'aperto'),
(6, 3, 'milano', 'udine', '2025-04-02', '10:00:00', 40.00, '4', 0, 'aperto'),
(7, 3, 'milano', 'torino', '2025-04-09', '10:00:00', 200.00, '1', 1, 'aperto');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `autisti`
--
ALTER TABLE `autisti`
  ADD PRIMARY KEY (`id_autista`),
  ADD KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD PRIMARY KEY (`id_prenotazione`),
  ADD KEY `id_viaggio` (`id_viaggio`),
  ADD KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id_utente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `viaggi`
--
ALTER TABLE `viaggi`
  ADD PRIMARY KEY (`id_viaggio`),
  ADD KEY `id_autista` (`id_autista`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `autisti`
--
ALTER TABLE `autisti`
  MODIFY `id_autista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  MODIFY `id_prenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `viaggi`
--
ALTER TABLE `viaggi`
  MODIFY `id_viaggio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `autisti`
--
ALTER TABLE `autisti`
  ADD CONSTRAINT `autisti_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id_utente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD CONSTRAINT `prenotazioni_ibfk_1` FOREIGN KEY (`id_viaggio`) REFERENCES `viaggi` (`id_viaggio`) ON DELETE CASCADE,
  ADD CONSTRAINT `prenotazioni_ibfk_2` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id_utente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `viaggi`
--
ALTER TABLE `viaggi`
  ADD CONSTRAINT `viaggi_ibfk_1` FOREIGN KEY (`id_autista`) REFERENCES `autisti` (`id_autista`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
