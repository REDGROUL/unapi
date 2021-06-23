-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 21 2021 г., 10:58
-- Версия сервера: 10.3.22-MariaDB
-- Версия PHP: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `umessenger`
--

-- --------------------------------------------------------

--
-- Структура таблицы `chanell_subs`
--

CREATE TABLE `chanell_subs` (
  `id` int(11) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `channel_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `channell_messages`
--

CREATE TABLE `channell_messages` (
  `id_channel` bigint(20) NOT NULL,
  `id_mess` bigint(20) NOT NULL,
  `message` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `channels`
--

CREATE TABLE `channels` (
  `id` bigint(20) NOT NULL,
  `channel_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access` set('public','private','only link','') COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_count` int(32) NOT NULL,
  `creator` bigint(20) NOT NULL,
  `content_managers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `link` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы ` conversation_dialogs`
--

CREATE TABLE ` conversation_dialogs` (
  `id` bigint(20) NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_users` bigint(20) NOT NULL,
  `photo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы ` conversation_dialogs`
--

INSERT INTO ` conversation_dialogs` (`id`, `name`, `description`, `count_users`, `photo`) VALUES
(1, 'test3', '', 1, ''),
(2, 'test3', '', 1, ''),
(3, 'test3', '', 1, 'files/images/60cb992ad6bf03.41308812.png'),
(4, 'test3', '', 1, 'files/images/60cb992ad6bf03.41308812.png');

-- --------------------------------------------------------

--
-- Структура таблицы ` conversation_messages`
--

CREATE TABLE ` conversation_messages` (
  `id` bigint(20) NOT NULL,
  `id_bigd` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `message` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы ` conversation_subs`
--

CREATE TABLE ` conversation_subs` (
  `bigd_id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `dialog`
--

CREATE TABLE `dialog` (
  `dialog_id` bigint(20) NOT NULL,
  `one_user_id` bigint(20) NOT NULL,
  `two_user_id` bigint(20) NOT NULL,
  `time_create` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` bigint(20) NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `hash_sum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_upload` datetime DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `dialog_id` bigint(20) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `sender` bigint(20) NOT NULL,
  `getter` bigint(20) DEFAULT NULL,
  `message_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_file` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_send` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `visible_one` tinyint(1) NOT NULL,
  `visible_two` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `report`
--

CREATE TABLE `report` (
  `report_id` bigint(20) NOT NULL,
  `report_user_id` bigint(20) NOT NULL,
  `reported_user_id` bigint(20) NOT NULL,
  `message` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_report` int(11) NOT NULL,
  `message_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `token`
--

CREATE TABLE `token` (
  `uid` bigint(20) NOT NULL,
  `token` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info_client` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `type_report`
--

CREATE TABLE `type_report` (
  `id` int(11) NOT NULL,
  `name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `login` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nick` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `agressive_push` tinyint(1) DEFAULT NULL,
  `role` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `chanell_subs`
--
ALTER TABLE `chanell_subs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_id` (`channel_id`);

--
-- Индексы таблицы `channell_messages`
--
ALTER TABLE `channell_messages`
  ADD PRIMARY KEY (`id_mess`),
  ADD KEY `id_channel` (`id_channel`);

--
-- Индексы таблицы `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator` (`creator`);

--
-- Индексы таблицы ` conversation_dialogs`
--
ALTER TABLE ` conversation_dialogs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы ` conversation_messages`
--
ALTER TABLE ` conversation_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bigd` (`id_bigd`,`uid`);

--
-- Индексы таблицы ` conversation_subs`
--
ALTER TABLE ` conversation_subs`
  ADD KEY `bigd_id` (`bigd_id`,`uid`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `dialog`
--
ALTER TABLE `dialog`
  ADD PRIMARY KEY (`dialog_id`),
  ADD KEY `one_user_id` (`one_user_id`,`two_user_id`),
  ADD KEY `two_user_id` (`two_user_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD KEY `message_id` (`message_id`),
  ADD KEY `sender` (`sender`),
  ADD KEY `getter` (`getter`);

--
-- Индексы таблицы `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `report_user_id` (`report_user_id`),
  ADD KEY `reported_user_id` (`reported_user_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `type_report` (`type_report`);

--
-- Индексы таблицы `token`
--
ALTER TABLE `token`
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `type_report`
--
ALTER TABLE `type_report`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `chanell_subs`
--
ALTER TABLE `chanell_subs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `channell_messages`
--
ALTER TABLE `channell_messages`
  MODIFY `id_mess` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `channels`
--
ALTER TABLE `channels`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы ` conversation_dialogs`
--
ALTER TABLE ` conversation_dialogs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы ` conversation_messages`
--
ALTER TABLE ` conversation_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dialog`
--
ALTER TABLE `dialog`
  MODIFY `dialog_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `report`
--
ALTER TABLE `report`
  MODIFY `report_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `type_report`
--
ALTER TABLE `type_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `chanell_subs`
--
ALTER TABLE `chanell_subs`
  ADD CONSTRAINT `chanell_subs_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`);

--
-- Ограничения внешнего ключа таблицы `channell_messages`
--
ALTER TABLE `channell_messages`
  ADD CONSTRAINT `channell_messages_ibfk_1` FOREIGN KEY (`id_channel`) REFERENCES `channels` (`id`);

--
-- Ограничения внешнего ключа таблицы `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `channels_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы ` conversation_messages`
--
ALTER TABLE ` conversation_messages`
  ADD CONSTRAINT ` conversation_messages_ibfk_1` FOREIGN KEY (`id_bigd`) REFERENCES ` conversation_dialogs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы ` conversation_subs`
--
ALTER TABLE ` conversation_subs`
  ADD CONSTRAINT ` conversation_subs_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT ` conversation_subs_ibfk_2` FOREIGN KEY (`bigd_id`) REFERENCES ` conversation_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `dialog`
--
ALTER TABLE `dialog`
  ADD CONSTRAINT `dialog_ibfk_1` FOREIGN KEY (`one_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dialog_ibfk_2` FOREIGN KEY (`two_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`getter`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`report_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
