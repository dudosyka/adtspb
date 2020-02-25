# Личный кабинет
## Структура проекта
Проект разделён на 2 части:
- Фронт-энд (приложение Vue.js): `/src/vue-app/`
- Бэк-энд (GraphQL): `/src/graphql/`

## Настройка проекта и первый запуск
### Для бэк-энда:
1. Установить PHP 7.4.2, MySQL 5.7, Composer (см. [Какой софт надо установить?](https://github.com/yageorgiy/adtspb-pac#%D0%BA%D0%B0%D0%BA%D0%BE%D0%B9-%D1%81%D0%BE%D1%84%D1%82-%D0%BD%D0%B0%D0%B4%D0%BE-%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C))
2. Сконфигурировать PHP 7.4.2, добавить MySQLi и MySQL PDO библиотеки (как показано на рисунке):
    - Зайти в папку с установленным PHP, открыть файл `php.ini` (если такого не имеется, то скопировать файл `php.ini-production`, вставить в эту же папку и переименовать новый файл в `php.ini`):
    
        ![Пример](https://i.imgur.com/ar2cmty.png)
    - Убрать символ `;` в начале слова на строках 918 и 923:
    
        ![Пример](https://i.imgur.com/lQnjNg6.png)
3. Установить зависимости:
    - Зайти в папку `/src/graphql/`
    - Выполнить команду `composer install`. После этого должна появиться папка `/src/graphql/vendor/`.
4. Импортировать структуру базы данных в MySQL:
    - Запустить OpenServer
    - Зайти в phpmyadmin (`http://127.0.0.1/openserver/phpmyadmin/`), войти под пользователем `root` с пустым паролем.
    - Зайти во вкладку `import`, выбрать файл `app.sql` (который можно скачать из доски Trello), нажать `Вперёд`:
        ![Пример](https://i.imgur.com/YPtIHJe.png)

5. Создать файл конфигурации:
    - Зайти в папку `/src/graphql/`
    - Скопировать файл `config.example.json` в эту же папку и переименовать его в `config.json`.
    - Изменить поля `db_name` (имя базы данных), `db_host` (адрес базы данных), `db_user` (пользователь базы данных), `db_pass` (пароль базы данных), если они отличаются от настроек базы данных.
6. Запустить сервер (см. [Как мне тестировать продукт?](https://github.com/yageorgiy/adtspb-pac#%D0%BA%D0%B0%D0%BA-%D0%BC%D0%BD%D0%B5-%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82)). После чего можно тестировать GraphQL по адресу: `http://адрес.сайта/api.php`.

### Для фронт-энда:
1. Установить Node.js, Vue CLI (см. [Какой софт надо установить?](https://github.com/yageorgiy/adtspb-pac#%D0%BA%D0%B0%D0%BA%D0%BE%D0%B9-%D1%81%D0%BE%D1%84%D1%82-%D0%BD%D0%B0%D0%B4%D0%BE-%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C))
2. Зайти в папку `/src/vue-app/`
3. Установить зависимости: выполнить `npm install`
4. Запустить сервер: выполнить `npm run serve`. После чего можно тестировать приложение по адресу: `http://localhost:8080/`.

## Сборка
### Фронтэнд Vue.js
```bash
cd src/vue-app/
npm run build
```

### Бэкэнд GraphQL
Установка Phing через composer:
```bash
cd src/graphql/
composer install
```

Установка Phing на Ubuntu / Linux Mint / Debian:
```bash
sudo apt update
sudo apt install phing
```

Сборка на Windows:
```bash
cd src/graphql/
"vendor/phing/phing/bin/phing" build
```

Сборка на Ubuntu / Linux Mint / Debian:
```bash
cd src/graphql/
phing build
```

## ЧаВо

#### Какой софт надо установить?

1. **Редактор кода или IDE:** что удобнее будет, то и можно поставить, без разницы. Лучше всего поставить IDE Jetbrains PHPStorm и взять 30-ти дневную бесплатную версию, тогда будет намного легче работать. Либо юзать другие текстовые редакторы (Notepad++, SublineText 3, Visual Studio Code) или IDE'шки (подходят больше для фронтов: Adobe Brackets, Jetbrains Webstorm).
2. [Git](https://git-scm.com/downloads)
3. [GraphQL Playground](https://github.com/prisma-labs/graphql-playground/releases/tag/v1.8.10) _(по желанию)_

Для фронт-эндов:
- [Node.js 12.16.1 LTS](https://nodejs.org/dist/v12.16.1/node-v12.16.1-x64.msi )
- [Vue.js CLI](https://cli.vuejs.org/guide/installation.html ) **(устанавливать после установки Node.js)**

Для бэк-эндов:
- [PHP 7.4.2](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/) (устанавливать после установки PHP 7.4.2)
- [MySQL 5.7 сервер](https://dev.mysql.com/downloads/mysql/5.7.html)

Если не хочется ставить PHP и MySQL по отдельности, то можно поставить [OpenServer](https://ospanel.io/) или [Xampp](https://www.apachefriends.org/ru/download.html) - комплексное ПО, включающее в себя PHP и MySQL **(но Composer всё-же надо будет потом доставить)**.

#### Как мне тестировать продукт?
**Vue.js приложение.** Запустить в консоле `npm run serve` в папке `/src/vue-app/`. Зайти на `http://localhost:8080/`.

**GraphQL приложение.** Запустить в консоле - `"C:\путь\к\PHP 7.3\php.exe" -S localhost:8085 -t D:\путь\к\корневой\папке\проекта\src\graphql\public_html`. После чего, GraphQL будет доступен по адресу `http://localhost:8085/api.php`.

#### Где найти пример создания типа (сущности) для GraphQL?
Файл User.php хранится в `/src/graphql/Application/Type/UserType.php`. В той же папке следует создавать другие типы.

#### Как собрать проект?
См. пункт "Сборка". После проведённых операций в папке `/dist/` появятся все необходимые файлы собранной программы. Их можно загружать на хостинг и содержать в дальнейшем.

#### Где мне взять исходную базу данных SQL?
В задачах Trello, пункт "Структура БД" в блоке "Информация". База данных по умолчанию называется `app` (сам файл - `app.sql`).

#### Ошибка Uncaught Error: Class 'GraphQL\Application\Entity\EntityBase' not found in <...>\src\graphql\Application\Entity\Association.php:11
Скачайте обновленный файл `/src/graphql/Application/init.php` или добавьте строку `require_once __DIR__ . '/Entity/EntityBase.php';` __перед__ циклом foreach.
