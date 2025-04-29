-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: db
-- Время создания: Дек 17 2024 г., 06:32
-- Версия сервера: 10.9.3-MariaDB-1:10.9.3+maria~ubu2204
-- Версия PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `21024_med`
--

-- --------------------------------------------------------

--
-- Структура таблицы `appointment`
--

CREATE TABLE `appointment` (
  `id_appointment` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('В обработке','Одобрена','Отменена') NOT NULL DEFAULT 'В обработке',
  `id_user` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `id_doctor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `appointment`
--

INSERT INTO `appointment` (`id_appointment`, `date`, `time`, `status`, `id_user`, `id_service`, `id_doctor`) VALUES
(1, '2024-12-11', '12:00:00', 'Одобрена', 2, 13, 16),
(2, '2024-12-13', '08:00:00', 'Отменена', 2, 8, 7),
(3, '2024-12-12', '08:00:00', 'Одобрена', 2, 11, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `department`
--

CREATE TABLE `department` (
  `id_department` int(11) NOT NULL,
  `department_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `department`
--

INSERT INTO `department` (`id_department`, `department_name`) VALUES
(1, 'Диагностическое отделение'),
(2, 'Кардиологическое отделение'),
(3, 'Радиологическое отделение'),
(4, 'Лаборатория'),
(5, 'Терапевтическое отделение'),
(6, 'Педиатрическое отделение'),
(7, 'Неврологическое отделение'),
(8, 'Физиотерапевтическое отделение'),
(9, 'Эндокринологическое отделение'),
(10, 'Хирургическое отделение'),
(11, 'Дерматологическое отделение'),
(12, 'Стоматологическое отделение'),
(13, 'Психиатрическое отделение'),
(14, 'Офтальмологическое отделение'),
(15, 'Реабилитационное отделение');

-- --------------------------------------------------------

--
-- Структура таблицы `doctor`
--

CREATE TABLE `doctor` (
  `id_doctor` int(11) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `father_name` varchar(45) NOT NULL,
  `work_experience` int(11) NOT NULL,
  `id_specialization` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `doctor`
--

INSERT INTO `doctor` (`id_doctor`, `last_name`, `name`, `father_name`, `work_experience`, `id_specialization`) VALUES
(1, 'Иванов', 'Сергей', 'Петрович', 10, 1),
(2, 'Смирнова', 'Анна', 'Викторовна', 5, 1),
(3, 'Кузнецов', 'Алексей', 'Игоревич', 12, 2),
(4, 'Петрова', 'Екатерина', 'Сергеевна', 8, 2),
(5, 'Сидоров', 'Дмитрий', 'Александрович', 15, 3),
(6, 'Васильева', 'Ольга', 'Николаевна', 7, 3),
(7, 'Федоров', 'Николай', 'Васильевич', 20, 4),
(8, 'Михайлова', 'Светлана', 'Юрьевна', 3, 4),
(9, 'Сергеева', 'Мария', 'Дмитриевна', 6, 5),
(10, 'Лебедев', 'Артем', 'Владимирович', 4, 5),
(11, 'Коваленко', 'Ирина', 'Алексеевна', 9, 6),
(12, 'Григорьев', 'Павел', 'Васильевич', 11, 6),
(13, 'Орлов', 'Валентин', 'Николаевич', 13, 7),
(14, 'Кузьмина', 'Наталья', 'Викторовна', 5, 7),
(15, 'Романов', 'Тимур', 'Геннадиевич', 14, 8),
(16, 'Соколова', 'Анастасия', 'Игоревна', 6, 9),
(17, 'Дьяков', 'Владислав', 'Сергеевич', 10, 8),
(18, 'Ларина', 'Елена', 'Анатольевна', 8, 9),
(19, 'Семенова', 'Татьяна', 'Владимировна', 7, 10),
(20, 'Филиппов', 'Роман', 'Васильевич', 9, 11),
(21, 'Белов', 'Игорь', 'Андреевич', 5, 12),
(22, 'Никитина', 'Оксана', 'Юрьевна', 3, 10),
(23, 'Зайцева', 'Вероника', 'Сергеевна', 4, 11),
(24, 'Костин', 'Арсений', 'Валерьевич', 6, 12),
(25, 'Савельев', 'Алексей', 'Михайлович', 10, 13),
(26, 'Рябова', 'Надежда', 'Степановна', 7, 13),
(27, 'Климова', 'Лилия', 'Анатольевна', 3, 14),
(28, 'Фролов', 'Денис', 'Викторович', 5, 14),
(29, 'Громова', 'Светлана', 'Валерьевна', 4, 15),
(30, 'Соловьев ', 'Артемий ', 'Юрьевич ', 2, 15),
(31, 'Долгих ', 'Ярослав ', 'Андреевич ', 6, 16),
(32, 'Королева ', 'Вероника ', 'Алексеевна ', 5, 16),
(33, 'Никифоров ', 'Кирилл ', 'Степанович ', 8, 17),
(34, 'Гришаев ', 'Борис ', 'Владимирович ', 9, 17),
(35, 'Лебедева ', 'Инна ', 'Валентиновна ', 3, 18),
(36, 'Коваленко ', 'Максим ', 'Олегович ', 2, 18);

-- --------------------------------------------------------

--
-- Структура таблицы `service`
--

CREATE TABLE `service` (
  `id_service` int(11) NOT NULL,
  `service_name` varchar(60) NOT NULL,
  `price` double NOT NULL,
  `description` varchar(150) NOT NULL,
  `id_specialization` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `service`
--

INSERT INTO `service` (`id_service`, `service_name`, `price`, `description`, `id_specialization`) VALUES
(1, 'УЗИ органов брюшной полости', 1100, 'Ультразвуковое исследование для оценки состояния печени, желчного пузыря, поджелудочной железы и других органов.', 1),
(2, 'ЭКГ (электрокардиограмма)', 1000, 'Исследование электрической активности сердца для выявления различных заболеваний.', 2),
(3, 'МРТ головного мозга', 5500, 'Магнитно-резонансная томография для детального изучения структуры головного мозга.', 3),
(4, 'КТ грудной клетки', 4200, 'Компьютерная томография для диагностики заболеваний легких и сердца.', 3),
(5, 'Биохимический анализ крови', 800, 'Анализ для оценки работы внутренних органов и обмена веществ.', 18),
(6, 'Консультация терапевта', 1200, 'Первичный осмотр и рекомендации по лечению различных заболеваний.', 5),
(7, 'Иммунизация (вакцинация)', 700, 'Прививки для профилактики инфекционных заболеваний.', 5),
(8, 'Лабораторное исследование на COVID-19', 900, 'Тест на наличие вируса SARS-CoV-2 в организме.', 4),
(9, 'Консультация невролога', 1200, 'Осмотр и диагностика заболеваний нервной системы.', 7),
(10, 'Физиотерапия', 1500, 'Лечение с использованием физических факторов (УВЧ, лазер, магнит).', 17),
(11, 'Рентгенография грудной клетки', 1000, 'Рентгеновское исследование для оценки состояния легких и сердца.', 3),
(12, 'Консультация эндокринолога', 1200, 'Осмотр и лечение заболеваний эндокринной системы.', 8),
(13, 'Хирургическая операция (аппендицит)', 20000, 'Оперативное вмешательство по удалению аппендикса.', 9),
(14, 'Консультация дерматолога', 1200, 'Осмотр кожи для диагностики заболеваний и назначение лечения.', 10),
(15, 'Стоматологическая консультация', 1200, 'Осмотр зубов и десен, диагностика заболеваний полости рта.', 11),
(16, 'Психотерапия', 1200, 'Консультации и лечение психических расстройств и состояний.', 12),
(17, 'Офтальмологическое обследование', 1400, 'Осмотр глаз для диагностики заболеваний и подбора очков/контактов.', 14),
(18, 'Массаж лечебный', 1500, 'Процедура для снятия мышечного напряжения и улучшения кровообращения.', 15);

-- --------------------------------------------------------

--
-- Структура таблицы `specialization`
--

CREATE TABLE `specialization` (
  `id_specialization` int(11) NOT NULL,
  `specialization_name` varchar(45) NOT NULL,
  `id_department` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `specialization`
--

INSERT INTO `specialization` (`id_specialization`, `specialization_name`, `id_department`) VALUES
(1, 'УЗИ-специалист', 1),
(2, 'Кардиолог', 2),
(3, 'Радиолог', 3),
(4, 'Лаборант', 4),
(5, 'Терапевт', 5),
(6, 'Педиатр', 6),
(7, 'Невролог', 7),
(8, 'Эндокринолог', 9),
(9, 'Хирург', 10),
(10, 'Дерматолог', 11),
(11, 'Стоматолог', 12),
(12, 'Психотерапевт', 13),
(13, 'Психиатр', 13),
(14, 'Офтальмолог', 14),
(15, 'Массажист', 15),
(16, 'Реабилитолог', 15),
(17, 'Физиотерапевт', 8),
(18, 'Лаборант-биохимик', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `father_name` varchar(45) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `number` varchar(11) DEFAULT NULL,
  `number_polis` varchar(16) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(10) DEFAULT NULL,
  `role` enum('Администратор','Пациент') DEFAULT 'Пациент'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id_user`, `last_name`, `name`, `father_name`, `date_of_birth`, `number`, `number_polis`, `email`, `password`, `role`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', 'Администратор'),
(2, 'Москвитина', 'Ксения', 'Александровна', '2005-05-06', '79995556611', '1234123412341234', 'ksen@yandex.ru', '123456', 'Пациент');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id_appointment`),
  ADD KEY `doc_idx` (`id_doctor`),
  ADD KEY `us_idx` (`id_user`),
  ADD KEY `ser_idx` (`id_service`);

--
-- Индексы таблицы `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id_department`);

--
-- Индексы таблицы `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id_doctor`),
  ADD KEY `spec_idx` (`id_specialization`);

--
-- Индексы таблицы `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `specia_idx` (`id_specialization`);

--
-- Индексы таблицы `specialization`
--
ALTER TABLE `specialization`
  ADD PRIMARY KEY (`id_specialization`),
  ADD KEY `dep_idx` (`id_department`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id_appointment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `department`
--
ALTER TABLE `department`
  MODIFY `id_department` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id_doctor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `service`
--
ALTER TABLE `service`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `specialization`
--
ALTER TABLE `specialization`
  MODIFY `id_specialization` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `doc` FOREIGN KEY (`id_doctor`) REFERENCES `doctor` (`id_doctor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ser` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `us` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `spec` FOREIGN KEY (`id_specialization`) REFERENCES `specialization` (`id_specialization`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `specia` FOREIGN KEY (`id_specialization`) REFERENCES `specialization` (`id_specialization`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `specialization`
--
ALTER TABLE `specialization`
  ADD CONSTRAINT `dep` FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
