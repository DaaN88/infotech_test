# Запуск проекта Yii1 в Docker

Среда для разработки Yii1 на PHP 8.3 (Alpine) с nginx и MariaDB.

## Требования
- Docker и Docker Compose

## Структура
- `Dockerfile` — PHP-FPM 8.3 с расширениями Yii1, Composer внутри.
- `docker-compose.yaml` — сервисы `app`, `nginx`, `db`.
- `docker/php/php.ini` — PHP-настройки (лимиты, opcache, sessions).
- `docker/nginx/default.conf` — виртуальный хост, root `/var/www/html/web`.
- `docker/db/my.cnf` — базовый конфиг MariaDB (utf8mb4, bind 0.0.0.0).

## Быстрый старт
1. Поместите исходники Yii1 так, чтобы входной файл был в `web/index.php`.
2. (Опционально) поправьте креды БД в `docker-compose.yaml` и `protected/config/main.php`.
3. Соберите и запустите:
   ```bash
   docker compose up --build
   ```
4. Приложение будет доступно на http://localhost:8080
   - PHP-FPM слушает внутри на 9000.
   - MariaDB доступна на порту 3306 (root:root, infotek/infotek по умолчанию).

## Полезные команды
- Остановить стек: `docker compose down`
- Логи всех сервисов: `docker compose logs -f`
- Логи конкретного: `docker compose logs -f app`
- Запуск в фоне: `docker compose up -d`

## Настройки и монтирования
- Код монтируется в контейнер `app` по пути `/var/www/html`.
- `php.ini` монтируется в `app` как `/usr/local/etc/php/conf.d/zz-custom.ini`.
- Nginx конфиг монтируется в `/etc/nginx/conf.d/default.conf`.
- Данные MariaDB сохраняются в named volume `db_data`.

## Примечания
- В `php.ini` включён opcache (подходит и для дев); при необходимости уменьшите/отключите.
- `cgi.fix_pathinfo = 0` и `session.save_path=/tmp/sessions` соответствуют Dockerfile.
- Если нужен другой домен/порт — поменяйте `server_name` и маппинг порта в `docker-compose.yaml`.
