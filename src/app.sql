-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 24 2020 г., 12:38
-- Версия сервера: 5.7.25-log
-- Версия PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `app`
--
CREATE DATABASE IF NOT EXISTS `app` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `app`;

-- --------------------------------------------------------

--
-- Структура таблицы `action`
--

DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Права (функции)';

--
-- Дамп данных таблицы `action`
--

INSERT INTO `action` (`id`, `name`, `description`) VALUES
(1, 'Просмотр расписания', 'Тестовая функция. Данная функция отвечает за это и за то.'),
(2, 'Редактирование расписание', 'Тестовая функция. Данная функция отвечает за это и за то.');

-- --------------------------------------------------------

--
-- Структура таблицы `action_list`
--

DROP TABLE IF EXISTS `action_list`;
CREATE TABLE `action_list` (
  `id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL COMMENT 'ID списка с правами',
  `role_id` bigint(20) NOT NULL COMMENT 'ID роли, которой доступна функция',
  `action_id` bigint(20) NOT NULL COMMENT 'ID права (функции)',
  `sign` enum('+','-') NOT NULL COMMENT 'Флаг (разрешено/запрещено)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица прав (функций), связка с ролями';

--
-- Дамп данных таблицы `action_list`
--

INSERT INTO `action_list` (`id`, `list_id`, `role_id`, `action_id`, `sign`) VALUES
(1, 1, 1, 1, '+'),
(2, 1, 2, 2, '+'),
(3, 1, 1, 2, '-');

-- --------------------------------------------------------

--
-- Структура таблицы `association`
--

DROP TABLE IF EXISTS `association`;
CREATE TABLE `association` (
  `id` bigint(20) NOT NULL,
  `name` text,
  `min_age` tinyint(4) DEFAULT NULL,
  `max_age` tinyint(4) DEFAULT NULL,
  `required_association_id` bigint(20) DEFAULT NULL COMMENT 'Предыдущий курс, который необходимо пройти.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ассоциация, направление в обучении';

--
-- Дамп данных таблицы `association`
--

INSERT INTO `association` (`id`, `name`, `min_age`, `max_age`, `required_association_id`) VALUES
(1, 'Робототехника', 12, 16, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` bigint(20) NOT NULL,
  `association_id` bigint(20) NOT NULL COMMENT 'ID ассоциации',
  `teacher_id` bigint(20) NOT NULL COMMENT 'ID учителя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Сформированные группы';

-- --------------------------------------------------------

--
-- Структура таблицы `group_timetable`
--

DROP TABLE IF EXISTS `group_timetable`;
CREATE TABLE `group_timetable` (
  `id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL COMMENT 'ID группы (из таблицы groups)',
  `day` enum('пн','вт','ср','чт','пт','сб','вс') NOT NULL COMMENT 'День недели',
  `time_start` tinytext NOT NULL COMMENT 'Время начала занятий (ЧЧ:ММ)',
  `time_end` tinytext NOT NULL COMMENT 'Время конца занятий (ЧЧ:ММ)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Дни посещения у групп (время и дни недели).';

-- --------------------------------------------------------

--
-- Структура таблицы `mailing`
--

DROP TABLE IF EXISTS `mailing`;
CREATE TABLE `mailing` (
  `id` bigint(20) NOT NULL,
  `started_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Время начала рассылки',
  `author_id` bigint(20) DEFAULT NULL COMMENT 'ID автора рассылки',
  `target_id` bigint(20) NOT NULL COMMENT 'ID пользователя, для которого прийдет уведомление',
  `text` mediumtext NOT NULL COMMENT 'Содержание (текст) рассылки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Рассылки';

--
-- Дамп данных таблицы `mailing`
--

INSERT INTO `mailing` (`id`, `started_date`, `author_id`, `target_id`, `text`) VALUES
(1, '2020-03-21 17:05:07', 1, 2, 'У нас завтра занятий нет.'),
(2, '2020-03-21 17:05:07', 1, 3, 'У нас завтра занятий нет.');

-- --------------------------------------------------------

--
-- Структура таблицы `proposal`
--

DROP TABLE IF EXISTS `proposal`;
CREATE TABLE `proposal` (
  `id` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата подачи',
  `child_id` bigint(20) NOT NULL COMMENT 'ID ребенка (таргет)',
  `parent_id` bigint(20) NOT NULL COMMENT 'ID родителя (автор)',
  `association_id` bigint(20) NOT NULL COMMENT 'ID объединения',
  `status_admin` enum('ожидание','отклонено','принято') NOT NULL DEFAULT 'ожидание' COMMENT 'Ответ от администратора',
  `status_parent` enum('ожидание','отклонено','принято') NOT NULL DEFAULT 'ожидание' COMMENT 'Ответ от родителя',
  `status_teacher` enum('ожидание','отклонено','принято') NOT NULL DEFAULT 'ожидание' COMMENT 'Ответ от учителя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `proposal`
--

INSERT INTO `proposal` (`id`, `timestamp`, `child_id`, `parent_id`, `association_id`, `status_admin`, `status_parent`, `status_teacher`) VALUES
(1, '2020-03-21 17:05:29', 2, 1, 1, 'ожидание', 'ожидание', 'ожидание');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` bigint(20) NOT NULL,
  `name` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Роли доступа';

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Пользователь'),
(2, 'Родитель'),
(3, 'Модератор'),
(4, 'Администратор'),
(5, 'Суперадминистратор');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL COMMENT 'Уникальный ID',
  `date_registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  `surname` text NOT NULL COMMENT 'Фамилия',
  `name` text NOT NULL COMMENT 'Имя',
  `midname` text NOT NULL COMMENT 'Отчество',
  `sex` enum('м','ж') NOT NULL COMMENT 'Пол',
  `phone_number` varchar(255) NOT NULL COMMENT 'Основной номер телефона, формат +7 (123) 123-45-67',
  `email` text NOT NULL COMMENT 'Основной e-mail',
  `status_email` enum('ожидание','подтвержден') NOT NULL COMMENT 'Статус подтверждения почты (ожидание = не подтвержден)',
  `verification_key_email` tinytext COMMENT 'Код подтверждения',
  `registration_address` text NOT NULL COMMENT 'Адрес регистрации',
  `residence_address` text COMMENT 'Адрес проживания',
  `job_place` text NOT NULL COMMENT 'Место работы',
  `job_position` text NOT NULL COMMENT 'Должность',
  `relationship` enum('родитель','ребенок') NOT NULL COMMENT 'Степень родства',
  `study_place` text NOT NULL COMMENT 'Адрес и номер школы (если есть)',
  `study_class` varchar(10) NOT NULL COMMENT 'Класс (если есть), формат: 1а - 11я',
  `birthday` date DEFAULT NULL COMMENT 'День рождения'
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COMMENT='Пользователи';

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `date_registered`, `surname`, `name`, `midname`, `sex`, `phone_number`, `email`, `status_email`, `verification_key_email`, `registration_address`, `residence_address`, `job_place`, `job_position`, `relationship`, `study_place`, `study_class`, `birthday`) VALUES
(1, '2020-02-22 23:59:37', 'Человеков', 'Человек', 'Человекович', 'м', '+ (123) 123-45-67', 'admin@site.com', 'ожидание', NULL, 'г. Спб, Улица Гармошкина, д. 12, к. 3', 'г. Спб, Улица Летчиков, д. 33, к. 5', 'г. Москва, Улица Доброделов, д. 1', 'Секретарь', 'родитель', '', '', '1982-10-10'),
(2, '2020-02-22 23:59:37', 'Примеров', 'Пример', 'Примерович', 'ж', '+7 (321) 222-33-21', 'user@somesite.org', 'подтвержден', NULL, 'г. Спб, ул. Декабристов, д. 3', 'г. Спб, пр. Мира, д. 6', '', '', 'ребенок', 'Школа №1', '8Б', '1982-10-10'),
(3, '2020-03-21 17:10:08', 'Лоремов', 'Лоремий', 'Ипсумович', 'ж', '+7 (321) 999-12-34', 'test@example.com', 'ожидание', NULL, 'г. Санкт-Петербург, ул. Грибоедова, д. 99, к. 90', 'г. Санкт-Петербург, ул. Центральная, д. 66, к. 44', '', '', 'ребенок', 'Школа №2', '3А', '2018-11-06');

-- --------------------------------------------------------

--
-- Структура таблицы `user_child`
--

DROP TABLE IF EXISTS `user_child`;
CREATE TABLE `user_child` (
  `id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL COMMENT 'ID родителя',
  `child_id` bigint(20) NOT NULL COMMENT 'ID ребенка'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка родителей с детьми';

--
-- Дамп данных таблицы `user_child`
--

INSERT INTO `user_child` (`id`, `parent_id`, `child_id`) VALUES
(1, 1, 2),
(2, 1, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `user_doc`
--

DROP TABLE IF EXISTS `user_doc`;
CREATE TABLE `user_doc` (
  `id` bigint(20) NOT NULL COMMENT 'Уникальный ID',
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя (кому принадлежит)',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания документа',
  `type` tinytext NOT NULL COMMENT 'Тип файла',
  `link` text NOT NULL COMMENT 'Путь к файлу',
  `data` text COMMENT 'Текстовые данные'
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COMMENT='Документы (в т.ч. паспорт, полис и пр.)';

--
-- Дамп данных таблицы `user_doc`
--

INSERT INTO `user_doc` (`id`, `user_id`, `date_created`, `type`, `link`, `data`) VALUES
(1, 1, '2020-03-21 17:19:16', 'passport', '/var/passport.png', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `user_group`
--

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка пользователей с группами';

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка пользователей с ролями';

--
-- Дамп данных таблицы `user_role`
--

INSERT INTO `user_role` (`id`, `role_id`, `user_id`) VALUES
(1, 2, 1),
(2, 1, 2),
(3, 1, 3);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `action_list`
--
ALTER TABLE `action_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Индексы таблицы `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`,`association_id`),
  ADD KEY `association_id` (`association_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Индексы таблицы `group_timetable`
--
ALTER TABLE `group_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Индексы таблицы `mailing`
--
ALTER TABLE `mailing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `target_id` (`target_id`);

--
-- Индексы таблицы `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_ibfk_1` (`association_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_child`
--
ALTER TABLE `user_child`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `user_doc`
--
ALTER TABLE `user_doc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `action`
--
ALTER TABLE `action`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `action_list`
--
ALTER TABLE `action_list`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `association`
--
ALTER TABLE `association`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `group`
--
ALTER TABLE `group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `group_timetable`
--
ALTER TABLE `group_timetable`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `mailing`
--
ALTER TABLE `mailing`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `proposal`
--
ALTER TABLE `proposal`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `user_child`
--
ALTER TABLE `user_child`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `user_doc`
--
ALTER TABLE `user_doc`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `action_list`
--
ALTER TABLE `action_list`
  ADD CONSTRAINT `action_list_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `action` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `action_list_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `group_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `group_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `group_timetable`
--
ALTER TABLE `group_timetable`
  ADD CONSTRAINT `group_timetable_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `mailing`
--
ALTER TABLE `mailing`
  ADD CONSTRAINT `mailing_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `mailing_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_child`
--
ALTER TABLE `user_child`
  ADD CONSTRAINT `user_child_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_child_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_doc`
--
ALTER TABLE `user_doc`
  ADD CONSTRAINT `user_doc_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_group`
--
ALTER TABLE `user_group`
  ADD CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_group_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
