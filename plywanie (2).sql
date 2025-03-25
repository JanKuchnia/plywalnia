-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 11:44 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plywanie`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `opiekun`
--

CREATE TABLE `opiekun` (
  `id_opiekuna` int(11) NOT NULL,
  `imie` varchar(40) DEFAULT NULL,
  `nazwisko` varchar(40) DEFAULT NULL,
  `id_szkoly` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opiekun`
--

INSERT INTO `opiekun` (`id_opiekuna`, `imie`, `nazwisko`, `id_szkoly`) VALUES
(1, 'kuba', 'klimczyk', 1),
(2, 'łukasz', 'sroka', 2),
(3, 'jan', 'kuchnia', 3),
(5, 'łukasz', 'sroka', 5),
(6, 'filip', 'gabrysiak', 5),
(7, 'asdas', 'asda', 5),
(8, 'Anna', 'Lipińska', 5),
(9, 'piotr', 'wątor', 5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `szkola`
--

CREATE TABLE `szkola` (
  `id_szkoly` int(11) NOT NULL,
  `nazwa` varchar(40) DEFAULT NULL,
  `miejscowosc` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `szkola`
--

INSERT INTO `szkola` (`id_szkoly`, `nazwa`, `miejscowosc`) VALUES
(1, 'Zespół szkół A.Średniawskiego', 'myślenice'),
(2, 'tytus', 'myślenice'),
(3, 'Rej', 'myślenice'),
(5, 'Średniawski', 'Małopolskie');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wynik`
--

CREATE TABLE `wynik` (
  `id_wyniku` int(11) NOT NULL,
  `id_szkoly` int(11) DEFAULT NULL,
  `czas` time DEFAULT NULL,
  `dystans` varchar(5) DEFAULT NULL,
  `data_plywania` date DEFAULT NULL,
  `id_zawodnik` int(11) DEFAULT NULL,
  `style` enum('kraul','styl klasyczny','motylkowy','grzbietowy') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wynik`
--

INSERT INTO `wynik` (`id_wyniku`, `id_szkoly`, `czas`, `dystans`, `data_plywania`, `id_zawodnik`, `style`) VALUES
(3, 1, '00:01:15', '100', '2025-02-01', 2, 'kraul'),
(4, 2, '00:01:12', '100', '2025-02-01', 3, 'motylkowy'),
(5, 3, '00:01:18', '100', '2025-02-01', 4, 'motylkowy'),
(7, 5, '00:01:00', '100', '2025-02-07', 6, 'grzbietowy'),
(8, 5, '00:01:50', '150', '2025-03-14', 7, 'kraul'),
(9, 5, '00:00:50', '150', '2025-03-14', 8, 'motylkowy'),
(10, 5, '00:00:40', '100', '2025-03-14', 9, 'styl klasyczny'),
(11, 5, '00:01:50', '150', '2025-03-18', 10, 'grzbietowy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zawodnik`
--

CREATE TABLE `zawodnik` (
  `id_zawodnik` int(11) NOT NULL,
  `imie` varchar(40) DEFAULT NULL,
  `nazwisko` varchar(40) DEFAULT NULL,
  `id_szkoly` int(11) DEFAULT NULL,
  `id_opiekuna` int(11) DEFAULT NULL,
  `rok_urodzenia` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zawodnik`
--

INSERT INTO `zawodnik` (`id_zawodnik`, `imie`, `nazwisko`, `id_szkoly`, `id_opiekuna`, `rok_urodzenia`) VALUES
(2, 'anna', 'lipinska', 1, 1, '2008'),
(3, 'viktor', 'migner', 2, 2, '2008'),
(4, 'filip', 'gabrysiak', 3, 3, '2008'),
(6, 'jan', 'kuchnia', 5, 5, '2008'),
(7, 'jakub', 'guśpiel', 5, 6, '2008'),
(8, 'sdga', 'aasdas', 5, 7, '2025'),
(9, 'Bartosz', 'Szuba', 5, 8, '2009'),
(10, 'krystian', 'ulman', 5, 9, '2008');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zgloszenie`
--

CREATE TABLE `zgloszenie` (
  `id_zgloszenia` int(11) NOT NULL,
  `id_zawodnik` int(11) DEFAULT NULL,
  `id_szkoly` int(11) DEFAULT NULL,
  `id_opiekuna` int(11) DEFAULT NULL,
  `id_wyniku` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zgloszenie`
--

INSERT INTO `zgloszenie` (`id_zgloszenia`, `id_zawodnik`, `id_szkoly`, `id_opiekuna`, `id_wyniku`) VALUES
(1, 2, 1, 1, NULL),
(2, 3, 2, 2, NULL),
(3, 4, 3, 3, NULL),
(4, 6, 5, 5, 7),
(5, 7, 5, 6, 8),
(7, 9, 5, 8, 10),
(8, 10, 5, 9, 11);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `opiekun`
--
ALTER TABLE `opiekun`
  ADD PRIMARY KEY (`id_opiekuna`),
  ADD KEY `id_szkoly` (`id_szkoly`);

--
-- Indeksy dla tabeli `szkola`
--
ALTER TABLE `szkola`
  ADD PRIMARY KEY (`id_szkoly`);

--
-- Indeksy dla tabeli `wynik`
--
ALTER TABLE `wynik`
  ADD PRIMARY KEY (`id_wyniku`),
  ADD KEY `id_szkoly` (`id_szkoly`),
  ADD KEY `id_zawodnik` (`id_zawodnik`);

--
-- Indeksy dla tabeli `zawodnik`
--
ALTER TABLE `zawodnik`
  ADD PRIMARY KEY (`id_zawodnik`),
  ADD KEY `id_szkoly` (`id_szkoly`),
  ADD KEY `id_opiekuna` (`id_opiekuna`);

--
-- Indeksy dla tabeli `zgloszenie`
--
ALTER TABLE `zgloszenie`
  ADD PRIMARY KEY (`id_zgloszenia`),
  ADD KEY `id_wyniku` (`id_wyniku`),
  ADD KEY `id_szkoly` (`id_szkoly`),
  ADD KEY `id_opiekuna` (`id_opiekuna`),
  ADD KEY `id_zawodnik` (`id_zawodnik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `opiekun`
--
ALTER TABLE `opiekun`
  MODIFY `id_opiekuna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `szkola`
--
ALTER TABLE `szkola`
  MODIFY `id_szkoly` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wynik`
--
ALTER TABLE `wynik`
  MODIFY `id_wyniku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `zawodnik`
--
ALTER TABLE `zawodnik`
  MODIFY `id_zawodnik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `zgloszenie`
--
ALTER TABLE `zgloszenie`
  MODIFY `id_zgloszenia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `opiekun`
--
ALTER TABLE `opiekun`
  ADD CONSTRAINT `opiekun_ibfk_1` FOREIGN KEY (`id_szkoly`) REFERENCES `szkola` (`id_szkoly`);

--
-- Constraints for table `wynik`
--
ALTER TABLE `wynik`
  ADD CONSTRAINT `wynik_ibfk_1` FOREIGN KEY (`id_szkoly`) REFERENCES `szkola` (`id_szkoly`),
  ADD CONSTRAINT `wynik_ibfk_2` FOREIGN KEY (`id_zawodnik`) REFERENCES `zawodnik` (`id_zawodnik`);

--
-- Constraints for table `zawodnik`
--
ALTER TABLE `zawodnik`
  ADD CONSTRAINT `zawodnik_ibfk_1` FOREIGN KEY (`id_szkoly`) REFERENCES `szkola` (`id_szkoly`),
  ADD CONSTRAINT `zawodnik_ibfk_2` FOREIGN KEY (`id_opiekuna`) REFERENCES `opiekun` (`id_opiekuna`);

--
-- Constraints for table `zgloszenie`
--
ALTER TABLE `zgloszenie`
  ADD CONSTRAINT `zgloszenie_ibfk_1` FOREIGN KEY (`id_wyniku`) REFERENCES `wynik` (`id_wyniku`),
  ADD CONSTRAINT `zgloszenie_ibfk_2` FOREIGN KEY (`id_szkoly`) REFERENCES `szkola` (`id_szkoly`),
  ADD CONSTRAINT `zgloszenie_ibfk_3` FOREIGN KEY (`id_opiekuna`) REFERENCES `opiekun` (`id_opiekuna`),
  ADD CONSTRAINT `zgloszenie_ibfk_4` FOREIGN KEY (`id_zawodnik`) REFERENCES `zawodnik` (`id_zawodnik`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
