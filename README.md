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