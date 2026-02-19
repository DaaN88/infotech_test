# Запуск проекта Yii1 в Docker

В репозитории уже лежит базовое приложение Yii 1.1.32 (генератор `yiic webapp`) и окружение PHP 8.3 + nginx + MariaDB для разработки. Ядро Yii ставится через Composer в `vendor/yiisoft/yii`.

## Требования
- Docker и Docker Compose

## Структура
- `Dockerfile` — PHP-FPM 8.3 с расширениями Yii1, Composer внутри.
- `docker-compose.yaml` — сервисы `app`, `nginx`, `db`.
- `docker/php/php.ini` — PHP-настройки (лимиты, opcache, sessions).
- `docker/nginx/default.conf` — виртуальный хост, root `/var/www/html` (корень Yii-приложения).
- `docker/db/my.cnf` — базовый конфиг MariaDB (utf8mb4, bind 0.0.0.0).
- `index.php`, `protected/`, `themes/`, `css/` — сгенерированное демо-приложение.

## Быстрый старт
1. Установите зависимости (внутри контейнера, чтобы совпала версия PHP):
   ```bash
   docker compose run --rm app composer install
   ```
   или, если Composer есть локально: `composer install`
2. (Опционально) Импортируйте свой SQL-дамп:
   ```bash
   docker compose up -d db
   docker exec -i infotek_db mysql -u infotek -pinfotek infotek < path/to/dump.sql
   ```
3. Соберите и запустите все сервисы:
   ```bash
   docker compose up --build
   ```
4. Откройте http://localhost:8080 — загружается базовый Yii1 сайт.
   - PHP-FPM внутри слушает 9000.
   - MariaDB снаружи доступна на 3306 (`root/root`, пользователь `infotek/infotek`).

## Полезные команды
- Остановить стек: `docker compose down`
- Логи всех сервисов: `docker compose logs -f`
- Логи конкретного: `docker compose logs -f app`
- Перезапуск только nginx: `docker restart infotek_nginx`
- Запуск в фоне: `docker compose up -d`

## Настройки и монтирования
- Код монтируется в контейнер `app` по пути `/var/www/html`.
- `php.ini` монтируется в `app` как `/usr/local/etc/php/conf.d/zz-custom.ini`.
- Nginx конфиг монтируется в `/etc/nginx/conf.d/default.conf`.
- Данные MariaDB сохраняются в named volume `db_data`.
- Коннект к БД внутри PHP настроен в `protected/config/database.php` (`host=db`, база `infotek`, пользователь/пароль `infotek`).

## Примечания
- В `php.ini` включён opcache (подходит и для дев); при необходимости уменьшите/отключите.
- `cgi.fix_pathinfo = 0` и `session.save_path=/tmp/sessions` соответствуют Dockerfile.
- Если нужен другой домен/порт — поменяйте `server_name` и маппинг порта в `docker-compose.yaml`.
- При желании пересоздать чистое приложение используйте внутри контейнера `php vendor/yiisoft/yii/framework/yiic.php webapp /var/www/html`.
