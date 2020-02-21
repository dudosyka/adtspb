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
В задачах Trello, пункт "Структура БД" в блоке "Информация".
