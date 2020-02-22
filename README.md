# Личный кабинет
## Структура проекта
Проект разделён на 2 части:
- Фронт-энд (приложение Vue.js): `/src/vue-app/`
- Бэк-энд (GraphQL): `/src/graphql/`

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
- Сконфигурировать PHP 7.4.2, добавить MySQLi и MySQL PDO библиотеки (как показано на рисунке):
    1. Зайти в папку с установленным PHP, открыть файл `php.ini` (если такого не имеется, то скопировать файл `php.ini-production`, вставить в эту же папку и переименовать новый файл в `php.ini`):
    ![alt text](https://i.imgur.com/ar2cmty.png)
    2. Убрать символ `;` в начале слова на строках 918 и 923:
    ![alt text](https://i.imgur.com/lQnjNg6.png)
- [Composer](https://getcomposer.org/download/) (устанавливать после установки PHP 7.4.2)
- [MySQL 5.7 сервер](https://dev.mysql.com/downloads/mysql/5.7.html)

Если не хочется ставить PHP и MySQL по отдельности, то можно поставить [OpenServer](https://ospanel.io/) или [Xampp](https://www.apachefriends.org/ru/download.html) - комплексное ПО, включающее в себя PHP и MySQL **(но Composer всё-же надо будет потом доставить)**.

#### Как мне тестировать продукт?
**Vue.js приложение.** Запустить в консоле `npm run serve` в папке `/src/vue-app/`.

**GraphQL приложение.** Запустить локальный сервер, где корневая папка сайта: `/src/graphql/public_html/`. 

Путь к GraphQL: `http://адрес.сайта/api.php`.


_Самый простой способ запуска локального сервера_ - `"C:\путь\к\PHP 7.3\php.exe" -S localhost:8085 -t D:\путь\к\корневой\папке\проекта\src\graphql\public_html`. После чего, GraphQL будет доступен по адресу `http://localhost:8085/api.php`.

#### Где найти пример создания типа (сущности) для GraphQL?
Файл User.php хранится в `/src/graphql/Application/Type/UserType.php`. В той же папке следует создавать другие типы.

#### Как собрать проект?
См. пункт "Сборка". После проведённых операций в папке `/dist/` появятся все необходимые файлы собранной программы. Их можно загружать на хостинг и содержать в дальнейшем.

#### Где мне взять исходную базу данных SQL?
В задачах Trello, пункт "Структура БД" в блоке "Информация". База данных по умолчанию называется `app` (сам файл - `app.sql`).


[Git]: https://git-scm.com/downloads