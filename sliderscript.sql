-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 31 Oca 2021, 22:42:24
-- Sunucu sürümü: 10.2.10-MariaDB
-- PHP Sürümü: 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `sliderscript`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `login_attempts`
--

CREATE TABLE `login_attempts` (
  `userId` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `verify` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `slider_contents`
--

CREATE TABLE `slider_contents` (
  `id` int(11) NOT NULL,
  `youtube_id` varchar(64) COLLATE utf8mb4_turkish_ci NOT NULL,
  `file_path` varchar(128) COLLATE utf8mb4_turkish_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
  `title_color` varchar(32) COLLATE utf8mb4_turkish_ci NOT NULL,
  `title_font` varchar(32) COLLATE utf8mb4_turkish_ci NOT NULL,
  `title_font_size` int(11) NOT NULL,
  `sub_title` varchar(512) COLLATE utf8mb4_turkish_ci NOT NULL,
  `sub_title_color` varchar(32) COLLATE utf8mb4_turkish_ci NOT NULL,
  `sub_title_font` varchar(32) COLLATE utf8mb4_turkish_ci NOT NULL,
  `sub_title_font_size` int(11) NOT NULL,
  `title_animation` varchar(64) COLLATE utf8mb4_turkish_ci NOT NULL,
  `sub_title_animation` varchar(64) COLLATE utf8mb4_turkish_ci NOT NULL,
  `text_direction` varchar(32) COLLATE utf8mb4_turkish_ci NOT NULL,
  `status` enum('A','P') COLLATE utf8mb4_turkish_ci NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `slider_settings`
--

CREATE TABLE `slider_settings` (
  `id` int(11) NOT NULL,
  `field` varchar(64) COLLATE utf8mb4_turkish_ci NOT NULL,
  `value` varchar(64) COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('SUPER ADMIN','ADMIN','USER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `type`) VALUES
(1, 'sliderScriptAdmin', '$2y$10$Reoy7aEtmm.n8kCxWFleNO7.JB9d/ItsS3jdNn4hWO5nzJnC0ErL.', '', 'Admin', 'SUPER ADMIN');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `slider_contents`
--
ALTER TABLE `slider_contents`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `slider_settings`
--
ALTER TABLE `slider_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `field` (`field`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `slider_contents`
--
ALTER TABLE `slider_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `slider_settings`
--
ALTER TABLE `slider_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
