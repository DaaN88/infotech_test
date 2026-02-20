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
1. Сбейлдите образы:
   ```bash
   docker compose build
   ```
2. Поднимите контейнеры:
   ```bash
   docker compose up -d -p infotech-app
   ```
3. Установите зависимости внутри контейнера приложения (ядро Yii попадёт в `vendor/` на хосте, т.к. код смонтирован):
   ```bash
   docker compose exec -it app sh
   composer install
   exit
   ```
   или короче: `docker compose exec app composer install`
4. Примените миграции:
   ```bash
   docker compose exec app php protected/yiic migrate --interactive=0
   ```
5. (Опционально) Импортируйте свой SQL-дамп (после старта db):
   ```bash
   docker exec -i infotek_db mysql -u infotek -pinfotek infotek < path/to/dump.sql
   ```
6. Откройте http://localhost:8080 — загружается базовый Yii1 сайт.
   - PHP-FPM внутри слушает 9000.
   - MariaDB снаружи доступна на 3306 (`root/root`, пользователь `infotek/infotek`).

## Полезные команды
- Остановить стек: `docker compose down`
- Логи всех сервисов: `docker compose logs -f`
- Логи конкретного: `docker compose logs -f app`
- Перезапуск только nginx: `docker restart infotek_nginx`
- Запуск в фоне: `docker compose up -d`
- Запуск только воркера очереди (внутри контейнера): `docker compose exec app php protected/yiic queue listen`
- Если видите ошибку `getaddrinfo for redis failed`, сначала поднимите Redis: `docker compose up -d redis` или установите `REDIS_HOST=127.0.0.1` при локальном запуске без Docker.

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

## Очередь и уведомления
- Redis поднимается как сервис `redis` (порт по умолчанию 6379, см. `.env`).
- API-ключ для SMS берётся из переменной `SMS_API_KEY` (есть в `.env` / `.env.example`, по умолчанию тестовый ключ smspilot-эмулятора).
- Воркеры:
  - Выполнить накопленные задачи и завершить: `docker compose exec app php protected/yiic queue run`
  - Слушать очередь постоянно: `docker compose exec app php protected/yiic queue listen` (рекомендуется запускать под supervisor/systemd).

## Тесты
Из контейнера приложения:
```bash
docker compose exec app php vendor/bin/phpunit --configuration protected/tests/phpunit.xml --testsuite unit
docker compose exec app php vendor/bin/phpunit --configuration protected/tests/phpunit.xml --testsuite functional
```
