-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 01 2020 г., 00:39
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

-- --------------------------------------------------------

--
-- Структура таблицы `action`
--

CREATE TABLE `action` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Права (функции)';

--
-- Дамп данных таблицы `action`
--

INSERT INTO `action` (`id`, `name`, `description`) VALUES
(1, 'Загрузка списка объединений', 'Позволяет загружать списки объединений на сервер.'),
(2, 'Загрузка списка педагогов', 'Позволяет загружать списки педагогов на сервер.'),
(3, 'Загрузка списка административных сотрудников', 'Позволяет загружать списки административных сотрудников на сервер.'),
(4, 'Регистрация ребенка', 'Регистрация ребенка от имени родителя.'),
(5, 'Просмотр списка объединений', 'Функция для просмотра списка объединений (ассоциаций).'),
(6, 'PDF заявление', 'Генерация поданных заявлений на группу в PDF'),
(7, 'Выбор групп ребенка', 'Выбор групп ребенка от лица родителя'),
(8, 'Состояние регистрации формы детей', 'Просмотр состояния наличия регистрации детей родителя в системе.'),
(9, 'Просмотр данных ребенка', 'Просмотр данных зарегистрированных детей (ребенка).');

-- --------------------------------------------------------

--
-- Структура таблицы `action_list`
--

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
(3, 1, 1, 2, '-'),
(4, 1, 5, 1, '+'),
(5, 1, 5, 3, '+'),
(6, 1, 5, 2, '+'),
(7, 1, 2, 4, '+'),
(8, 1, 2, 5, '+'),
(9, 1, 2, 6, '+'),
(10, 1, 2, 7, '+'),
(11, 1, 2, 8, '+'),
(12, 1, 2, 9, '+');

-- --------------------------------------------------------

--
-- Структура таблицы `association`
--

CREATE TABLE `association` (
  `id` bigint(20) NOT NULL,
  `name` text COMMENT 'Наименование объединения',
  `min_age` tinyint(4) DEFAULT NULL COMMENT 'Минимальный порог вхождения (включительно)',
  `max_age` tinyint(4) DEFAULT NULL COMMENT 'Максимальный порог вхождения (включительно)',
  `study_years` int(11) NOT NULL DEFAULT '1' COMMENT 'Количество лет обучения',
  `study_hours` int(11) NOT NULL COMMENT 'Общее количество часов в году',
  `study_hours_week` int(11) NOT NULL COMMENT 'Индексация: количество часов в неделю (ВСЕГО_ЧАСОВ / 9 мес. / 4 нед. в сред.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ассоциация, направление в обучении';

--
-- Дамп данных таблицы `association`
--

INSERT INTO `association` (`id`, `name`, `min_age`, `max_age`, `study_years`, `study_hours`, `study_hours_week`) VALUES
(1, 'Робототехника', 12, 17, 1, 200, 0),
(2, 'Робототехника', 11, 16, 1, 256, 4),
(3, 'Киберспорт', 11, 16, 1, 256, 2),
(4, 'Шахматы', 11, 16, 1, 256, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `association_requiredassociation`
--

CREATE TABLE `association_requiredassociation` (
  `id` bigint(20) NOT NULL,
  `association_id` bigint(20) NOT NULL COMMENT 'ID ассоциации',
  `required_association_id` bigint(20) NOT NULL COMMENT 'ID курса, который необходимо закончить'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `event`
--

CREATE TABLE `event` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL COMMENT 'Наименование мероприятия',
  `description` text NOT NULL COMMENT 'Описание мероприятия',
  `date_start` datetime NOT NULL COMMENT 'Дата начала',
  `date_end` datetime NOT NULL COMMENT 'Дата окончания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Мероприятия и эвенты';

--
-- Дамп данных таблицы `event`
--

INSERT INTO `event` (`id`, `name`, `description`, `date_start`, `date_end`) VALUES
(1, 'День знаний', 'Начни свое обучение вместе с Академией Цифровых Технологий.', '2020-09-01 12:00:00', '2020-09-01 17:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `event_file`
--

CREATE TABLE `event_file` (
  `id` bigint(20) NOT NULL,
  `event_id` bigint(20) NOT NULL COMMENT 'ID мероприятия',
  `category_id` bigint(20) NOT NULL COMMENT 'ID раздела (типа) файла, напр: фото, документация, ...',
  `path` text NOT NULL COMMENT 'Путь к файлу'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка файлов с мероприятием';

--
-- Дамп данных таблицы `event_file`
--

INSERT INTO `event_file` (`id`, `event_id`, `category_id`, `path`) VALUES
(1, 1, 2, '/path/to/document.pdf'),
(2, 1, 1, '/path/to/photo.png');

-- --------------------------------------------------------

--
-- Структура таблицы `event_participant`
--

CREATE TABLE `event_participant` (
  `id` bigint(20) NOT NULL,
  `event_id` bigint(20) NOT NULL COMMENT 'ID мероприятия',
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка участников (пользователей) с мероприятием';

-- --------------------------------------------------------

--
-- Структура таблицы `event_speaker`
--

CREATE TABLE `event_speaker` (
  `id` bigint(20) NOT NULL,
  `event_id` bigint(20) NOT NULL COMMENT 'ID мероприятия',
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка спикеров (пользователей) с мероприятием';

-- --------------------------------------------------------

--
-- Структура таблицы `group`
--

CREATE TABLE `group` (
  `id` bigint(20) NOT NULL,
  `association_id` bigint(20) NOT NULL COMMENT 'ID ассоциации',
  `teacher_id` bigint(20) NOT NULL COMMENT 'ID учителя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Сформированные группы';

-- --------------------------------------------------------

--
-- Структура таблицы `group_timetable`
--

CREATE TABLE `group_timetable` (
  `id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL COMMENT 'ID группы (из таблицы groups)',
  `day` enum('пн','вт','ср','чт','пт','сб','вс') NOT NULL COMMENT 'День недели',
  `time_start` tinytext NOT NULL COMMENT 'Время начала занятий (ЧЧ:ММ)',
  `time_end` tinytext NOT NULL COMMENT 'Время конца занятий (ЧЧ:ММ)',
  `lessons_count` int(11) NOT NULL COMMENT 'Количество уроков за год',
  `week_times` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Коэффициент пропуска недели (занятие каждые N недель)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Дни посещения у групп (время и дни недели).';

-- --------------------------------------------------------

--
-- Структура таблицы `group_timetableexception`
--

CREATE TABLE `group_timetableexception` (
  `id` bigint(20) NOT NULL,
  `date_start` datetime NOT NULL COMMENT 'Новая дата начала занятий',
  `date_end` datetime NOT NULL COMMENT 'Новая дата окончания',
  `status_id` bigint(20) NOT NULL COMMENT 'ID статуса (перенос, отмена, ...)',
  `moved_from_date_start` datetime NOT NULL COMMENT 'Предыдущая дата, перенос с даты начала'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Временные исключения (изменения) в расписании';

--
-- Дамп данных таблицы `group_timetableexception`
--

INSERT INTO `group_timetableexception` (`id`, `date_start`, `date_end`, `status_id`, `moved_from_date_start`) VALUES
(1, '2020-03-27 13:20:00', '2020-03-27 15:20:00', 2, '2020-03-24 13:20:00');

-- --------------------------------------------------------

--
-- Структура таблицы `mailing`
--

CREATE TABLE `mailing` (
  `id` bigint(20) NOT NULL,
  `started_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Время начала рассылки',
  `author_id` bigint(20) DEFAULT NULL COMMENT 'ID автора рассылки',
  `target_id` bigint(20) NOT NULL COMMENT 'ID пользователя, для которого прийдет уведомление',
  `text` mediumtext NOT NULL COMMENT 'Содержание (текст) рассылки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Рассылки';

-- --------------------------------------------------------

--
-- Структура таблицы `passwordrestore`
--

CREATE TABLE `passwordrestore` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя',
  `key_code` text NOT NULL COMMENT 'Ключ к восстановлению пароля',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания запроса',
  `ip` text NOT NULL COMMENT 'IP, с которого пытаются восстановить пароль'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Запросы пользователей на восстановление пароля';

-- --------------------------------------------------------

--
-- Структура таблицы `proposal`
--

CREATE TABLE `proposal` (
  `id` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата подачи',
  `child_id` bigint(20) NOT NULL COMMENT 'ID ребенка (таргет)',
  `parent_id` bigint(20) NOT NULL COMMENT 'ID родителя (автор)',
  `association_id` bigint(20) NOT NULL COMMENT 'ID объединения',
  `status_admin_id` bigint(20) NOT NULL COMMENT 'Ответ от администратора',
  `status_parent_id` bigint(20) NOT NULL COMMENT 'Ответ от родителя',
  `status_teacher_id` bigint(20) NOT NULL COMMENT 'Ответ от учителя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `proposal`
--

INSERT INTO `proposal` (`id`, `timestamp`, `child_id`, `parent_id`, `association_id`, `status_admin_id`, `status_parent_id`, `status_teacher_id`) VALUES
(1, '2020-05-29 22:18:30', 2, 1, 2, 1, 4, 1),
(2, '2020-05-29 22:18:30', 2, 1, 3, 1, 4, 1),
(3, '2020-05-30 22:55:49', 4, 3, 2, 1, 4, 1),
(4, '2020-05-30 22:55:49', 4, 3, 3, 1, 4, 1),
(5, '2020-05-30 22:55:49', 5, 3, 1, 1, 4, 1),
(6, '2020-05-30 22:55:49', 5, 3, 3, 1, 4, 1),
(7, '2020-05-30 22:55:49', 6, 3, 1, 1, 4, 1),
(8, '2020-05-30 22:55:49', 6, 3, 3, 1, 4, 1),
(9, '2020-05-30 22:55:49', 7, 3, 2, 1, 4, 1),
(10, '2020-05-30 22:55:49', 7, 3, 3, 1, 4, 1),
(11, '2020-05-30 23:05:44', 4, 3, 1, 1, 4, 1),
(12, '2020-05-30 23:05:44', 7, 3, 1, 1, 4, 1),
(13, '2020-05-30 23:09:12', 5, 3, 4, 1, 4, 1),
(14, '2020-05-30 23:10:05', 4, 3, 4, 1, 4, 1),
(15, '2020-05-30 23:10:05', 6, 3, 4, 1, 4, 1),
(16, '2020-05-31 21:19:35', 9, 8, 2, 1, 4, 1),
(17, '2020-05-31 21:19:35', 9, 8, 3, 1, 4, 1),
(18, '2020-05-31 21:23:17', 9, 8, 1, 1, 4, 1),
(19, '2020-05-31 21:30:21', 9, 8, 4, 1, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

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
(5, 'Супермодератор'),
(6, 'Ребенок'),
(7, 'Педагог');

-- --------------------------------------------------------

--
-- Структура таблицы `settings_eventfiletype`
--

CREATE TABLE `settings_eventfiletype` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL COMMENT 'Наименование категории файла'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки: категории файлов для мероприятий';

--
-- Дамп данных таблицы `settings_eventfiletype`
--

INSERT INTO `settings_eventfiletype` (`id`, `name`) VALUES
(1, 'Фото мероприятия'),
(2, 'Документация'),
(3, 'Для заполнения');

-- --------------------------------------------------------

--
-- Структура таблицы `settings_proposal`
--

CREATE TABLE `settings_proposal` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки: статусы подачи заявлений';

--
-- Дамп данных таблицы `settings_proposal`
--

INSERT INTO `settings_proposal` (`id`, `name`) VALUES
(1, 'Ожидание'),
(2, 'Принято'),
(3, 'Отозвано'),
(4, 'Подано'),
(5, 'Требуются уточнения'),
(6, 'Принесено'),
(7, 'Отклонено'),
(8, 'Зачислен');

-- --------------------------------------------------------

--
-- Структура таблицы `settings_relationship`
--

CREATE TABLE `settings_relationship` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='(устарело) Настройки: родственная связь';

--
-- Дамп данных таблицы `settings_relationship`
--

INSERT INTO `settings_relationship` (`id`, `name`) VALUES
(1, 'Родитель'),
(2, 'Ребенок');

-- --------------------------------------------------------

--
-- Структура таблицы `settings_timetablestatus`
--

CREATE TABLE `settings_timetablestatus` (
  `id` bigint(20) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки: статусы исключений в расписании';

--
-- Дамп данных таблицы `settings_timetablestatus`
--

INSERT INTO `settings_timetablestatus` (`id`, `name`) VALUES
(1, 'Замещение занятий'),
(2, 'Перенос занятий'),
(3, 'Отмена занятий');

-- --------------------------------------------------------

--
-- Структура таблицы `upload`
--

CREATE TABLE `upload` (
  `id` bigint(20) NOT NULL,
  `type` text NOT NULL,
  `file` text NOT NULL,
  `ip` text NOT NULL COMMENT 'Для определенного IP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список загруженных файлов в хранилище приложения';

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL COMMENT 'Уникальный ID',
  `date_registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  `login` text NOT NULL COMMENT 'Логин пользователя в формате фамилияИО№',
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
  `relationship` text NOT NULL COMMENT 'Степень родства (родитель приходится ребенку как)',
  `study_place` text NOT NULL COMMENT 'Адрес и номер школы (если есть)',
  `study_class` text NOT NULL COMMENT 'Класс (если есть), формат: 1а - 11я',
  `birthday` date DEFAULT NULL COMMENT 'День рождения',
  `password` text NOT NULL COMMENT 'Пароль пользователя (шифруется приложением!)',
  `ovz` enum('да','нет','-') CHARACTER SET utf8mb4 NOT NULL COMMENT 'ОВЗ?',
  `registration_type` enum('да','нет','-') CHARACTER SET utf8mb4 NOT NULL COMMENT 'Постоянная регистрация? да = постоянная, нет = временная',
  `state` text NOT NULL COMMENT 'Гражданство (государство)'
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COMMENT='Пользователи';

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `date_registered`, `login`, `surname`, `name`, `midname`, `sex`, `phone_number`, `email`, `status_email`, `verification_key_email`, `registration_address`, `residence_address`, `job_place`, `job_position`, `relationship`, `study_place`, `study_class`, `birthday`, `password`, `ovz`, `registration_type`, `state`) VALUES
(0, '2020-05-28 22:57:15', 'anonymous', 'Аноним', 'Аноним', 'Аноним', 'м', '+7(000)000-00-00', 'anonymous@localhost', 'ожидание', NULL, '', NULL, '', '', '', '', '', '2020-05-14', '', 'нет', 'нет', 'РФ'),
(1, '2020-05-29 22:16:25', 'testovtt', 'Тестов', 'Тест', 'Тестович', 'м', '+7(444)111-12-55', 'admin@testov1.com', 'ожидание', '704ACC68', 'Россия, Москва, Центральный административный округ, Пресненский район, Московский международный деловой центр Москва-Сити ', 'Россия, Санкт-Петербург, Петродворцовый район, посёлок Стрельна, Санкт-Петербургское шоссе ', 'Место работы', 'Должность', '', '', '', '1973-12-12', '$2y$12$mquPInmKFNxG95sNNwTR3.kCtatyS73ELk7PLOvhVcW9rOMAcXkjW', '-', '-', ''),
(2, '2020-05-29 22:18:16', 'ivanovip', 'Иванов', 'Иван', 'Петрович', 'м', '+7(999)999-88-44', 'admin@testov.com', 'ожидание', '', 'Россия, Москва, Центральный административный округ, Пресненский район, Московский международный деловой центр Москва-Сити ', 'Россия, Санкт-Петербург, Петродворцовый район, посёлок Стрельна, Санкт-Петербургское шоссе ', '', '', 'Родитель', 'Школа №123', '1а', '2003-12-12', '$2y$12$un4Ew/LMdZAhQqvplfgm4OUaYgW6XRIUOcteXHbcATgrIFDD6T.qi', 'нет', 'да', 'РФ'),
(3, '2020-05-30 22:04:01', 'opqowiepoqiwepoiqweqp', 'opqowiepoqiw[epoiqwe', 'qwpeiuqwpoieu', 'pqwieuqpwoieupqwoi', 'м', '+7(123)123-12-31', 'qwpeiuqpwioeuqwpioe@qweqwer.ur', 'подтвержден', '', 'owqiepqowie', '[oqiwe[poqwie', '', '', '', '', '', NULL, '$2y$12$.SviPDpR/Bx9sVYqJTaGFOCvXWEzVvKbi5JM6LbmKvVrJX/seo9KW', '-', '-', ''),
(4, '2020-05-30 22:35:12', 'testovtt2', 'Тестов', 'Тест', 'Тестович', 'м', '+7(123)123-12-32', '', 'ожидание', '', 'owqiepqowie', '[oqiwe[poqwie', '', '', 'Родитель', 'Школа 123', '1а', '2001-12-12', '$2y$12$AD5Jy7IFmvTfxVovwWdRjeP45IHpJbNrDJtXHFsCe9RUacCnDpvyK', 'нет', 'да', 'РФ'),
(5, '2020-05-30 22:36:57', 'testovtt3', 'Тестов', 'Тест', 'Тестович', 'м', '+7(123)123-12-31', '', 'ожидание', '', 'owqiepqowie', '[oqiwe[poqwie', '', '', 'Родитель', 'Школа 123', '1а', '2001-12-12', '$2y$12$jYsTmEIkawT3fckI0zP0vOeRSH.lIWDK33EM94tO1GftrLakFJrrm', 'нет', 'да', 'РФ'),
(6, '2020-05-30 22:54:42', 'testovtt4', 'Тестов', 'Тест', 'Тестович', 'м', '+7(123)123-12-32', '', 'ожидание', '', 'owqiepqowie', '[oqiwe[poqwie', '', '', 'Родитель', 'Школа 123', '1а', '2001-12-12', '$2y$12$4jcn2payQ/ADLc1mNCwbXef8VcUzm7FSbrsEEbEot0xwhwYRBKHve', 'нет', 'да', 'РФ'),
(7, '2020-05-30 22:54:42', 'testovtt5', 'Тестов', 'Тест', 'Тестович', 'м', '+7(123)123-12-31', '', 'ожидание', '', 'owqiepqowie', '[oqiwe[poqwie', '', '', 'Родитель', 'Школа 123', '1а', '2001-12-12', '$2y$12$a.K7TwsNwaKKJjCjDpzQDOIv9Bigxuq0BdidXUo7GnBRNhSxEUfwe', 'нет', 'да', 'РФ'),
(8, '2020-05-31 21:05:21', 'iquwpeoiuqpwoieuiu', 'iquwpeoiuqpwoieu', 'iquwpeioquwepo', 'upoiquwpeoiquwpeoiquwiopepuq', 'м', '+7(519)725-08-12', 'adnnnnnnnn@qweuqoiwrpoquwrqwr.ru', 'подтвержден', '', '1247091827', '081724987102984', '', '', '', '', '', NULL, '$2y$12$lBh8TpuqHSO8mahGkOzKceClt9tTWrGS7FN6QETDIsvWHYoUjmZ0W', '-', '-', ''),
(9, '2020-05-31 21:18:45', 'qweqq', 'qwe', 'qweqweqwe', 'qweqweqweqwe', 'м', '+7(152)180-97-51', 'admin1111@testov.com', 'ожидание', '', '1247091827', '081724987102984', '', '', 'Родитель', 'qwe', '123', '2003-12-12', '$2y$12$q/mFH4IbfiWJboRwUukR8.NR0xcJVG5ua6LKr/Yx7vC1LvYTTMJj.', 'нет', 'да', 'РФ');

-- --------------------------------------------------------

--
-- Структура таблицы `user_child`
--

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
(2, 3, 4),
(3, 3, 5),
(4, 3, 6),
(5, 3, 7),
(6, 8, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `user_doc`
--

CREATE TABLE `user_doc` (
  `id` bigint(20) NOT NULL COMMENT 'Уникальный ID',
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя (кому принадлежит)',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания документа',
  `type` tinytext NOT NULL COMMENT 'Тип файла',
  `link` text NOT NULL COMMENT 'Путь к файлу',
  `data` text COMMENT 'Текстовые данные'
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COMMENT='Документы (в т.ч. паспорт, полис и пр.)';

-- --------------------------------------------------------

--
-- Структура таблицы `user_group`
--

CREATE TABLE `user_group` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связка пользователей с группами';

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

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
(2, 6, 2),
(3, 2, 3),
(4, 6, 4),
(5, 6, 5),
(6, 6, 6),
(7, 6, 7),
(8, 2, 8),
(9, 6, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `user_token`
--

CREATE TABLE `user_token` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL COMMENT 'Токен',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время создания',
  `user_id` bigint(20) NOT NULL COMMENT 'ID пользователя, которому принадлежит токен'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Токены пользователей для доступа с API (выд. посл. авториз.)';

--
-- Дамп данных таблицы `user_token`
--

INSERT INTO `user_token` (`id`, `token`, `date_created`, `user_id`) VALUES
(1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoidWlkMSJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4NSIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDg1IiwianRpIjoidWlkMSIsImlhdCI6MTU5MDc5MDY0OCwibmJmIjoxNTkwNzk0MjQ4LCJleHAiOjE1OTA4NzcwNDgsInVpZCI6MSwiaXAiOiIxMjcuMC4wLjEifQ.', '2020-05-29 22:17:28', 1),
(2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoidWlkMyJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4NSIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDg1IiwianRpIjoidWlkMyIsImlhdCI6MTU5MDg3NjMxMSwibmJmIjoxNTkwODc5OTExLCJleHAiOjE1OTA5NjI3MTEsInVpZCI6MywiaXAiOiIxMjcuMC4wLjEifQ.', '2020-05-30 22:05:12', 3),
(3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoidWlkOCJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4NSIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDg1IiwianRpIjoidWlkOCIsImlhdCI6MTU5MDk1OTIxNSwibmJmIjoxNTkwOTYyODE1LCJleHAiOjE1OTEwNDU2MTUsInVpZCI6OCwiaXAiOiIxMjcuMC4wLjEifQ.', '2020-05-31 21:06:55', 8),
(4, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoidWlkOCJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4NSIsImF1ZCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDg1IiwianRpIjoidWlkOCIsImlhdCI6MTU5MDk1OTg5NCwibmJmIjoxNTkwOTYzNDk0LCJleHAiOjE1OTEwNDYyOTQsInVpZCI6OCwiaXAiOiIxMjcuMC4wLjEifQ.', '2020-05-31 21:18:14', 8);

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
-- Индексы таблицы `association_requiredassociation`
--
ALTER TABLE `association_requiredassociation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `association_id` (`association_id`),
  ADD KEY `required_association_id` (`required_association_id`);

--
-- Индексы таблицы `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `event_file`
--
ALTER TABLE `event_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Индексы таблицы `event_participant`
--
ALTER TABLE `event_participant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `event_speaker`
--
ALTER TABLE `event_speaker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Индексы таблицы `group_timetableexception`
--
ALTER TABLE `group_timetableexception`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `mailing`
--
ALTER TABLE `mailing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `target_id` (`target_id`);

--
-- Индексы таблицы `passwordrestore`
--
ALTER TABLE `passwordrestore`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_ibfk_1` (`association_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `status_admin_id` (`status_admin_id`),
  ADD KEY `status_parent_id` (`status_parent_id`),
  ADD KEY `status_teacher_id` (`status_teacher_id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings_eventfiletype`
--
ALTER TABLE `settings_eventfiletype`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings_proposal`
--
ALTER TABLE `settings_proposal`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings_relationship`
--
ALTER TABLE `settings_relationship`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings_timetablestatus`
--
ALTER TABLE `settings_timetablestatus`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `upload`
--
ALTER TABLE `upload`
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
-- Индексы таблицы `user_token`
--
ALTER TABLE `user_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `action`
--
ALTER TABLE `action`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `action_list`
--
ALTER TABLE `action_list`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `association`
--
ALTER TABLE `association`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `association_requiredassociation`
--
ALTER TABLE `association_requiredassociation`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `event`
--
ALTER TABLE `event`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `event_file`
--
ALTER TABLE `event_file`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `event_participant`
--
ALTER TABLE `event_participant`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `event_speaker`
--
ALTER TABLE `event_speaker`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT для таблицы `group_timetableexception`
--
ALTER TABLE `group_timetableexception`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `mailing`
--
ALTER TABLE `mailing`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `passwordrestore`
--
ALTER TABLE `passwordrestore`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `proposal`
--
ALTER TABLE `proposal`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `settings_eventfiletype`
--
ALTER TABLE `settings_eventfiletype`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `settings_proposal`
--
ALTER TABLE `settings_proposal`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `settings_relationship`
--
ALTER TABLE `settings_relationship`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `settings_timetablestatus`
--
ALTER TABLE `settings_timetablestatus`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `upload`
--
ALTER TABLE `upload`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `user_child`
--
ALTER TABLE `user_child`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `user_doc`
--
ALTER TABLE `user_doc`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный ID';

--
-- AUTO_INCREMENT для таблицы `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `user_token`
--
ALTER TABLE `user_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Ограничения внешнего ключа таблицы `association_requiredassociation`
--
ALTER TABLE `association_requiredassociation`
  ADD CONSTRAINT `association_requiredassociation_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `association_requiredassociation_ibfk_2` FOREIGN KEY (`required_association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `event_file`
--
ALTER TABLE `event_file`
  ADD CONSTRAINT `event_file_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `settings_eventfiletype` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `event_file_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `event_participant`
--
ALTER TABLE `event_participant`
  ADD CONSTRAINT `event_participant_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `event_participant_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `event_speaker`
--
ALTER TABLE `event_speaker`
  ADD CONSTRAINT `event_speaker_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `event_speaker_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

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
-- Ограничения внешнего ключа таблицы `group_timetableexception`
--
ALTER TABLE `group_timetableexception`
  ADD CONSTRAINT `group_timetableexception_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `settings_timetablestatus` (`id`) ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `mailing`
--
ALTER TABLE `mailing`
  ADD CONSTRAINT `mailing_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `mailing_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `passwordrestore`
--
ALTER TABLE `passwordrestore`
  ADD CONSTRAINT `passwordrestore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_4` FOREIGN KEY (`status_admin_id`) REFERENCES `settings_proposal` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_5` FOREIGN KEY (`status_parent_id`) REFERENCES `settings_proposal` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `proposal_ibfk_6` FOREIGN KEY (`status_teacher_id`) REFERENCES `settings_proposal` (`id`) ON UPDATE NO ACTION;

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

--
-- Ограничения внешнего ключа таблицы `user_token`
--
ALTER TABLE `user_token`
  ADD CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
