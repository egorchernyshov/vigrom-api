# Vigrom. Тестовое задание 

API для работы с кошельком пользователя

[Vigrom_PHP_Test.pdf](Vigrom_PHP_Test.pdf)


В тестах используется in-memory база данных, миграции применяются при запуске теста.

Для ручной проверки необходимо накатить миграции и заполнить базу тестовыми данными. В файле [requests.http](requests.http) образцы запросов.

Запуск сервисов:

```bash
# Start services
docker-compose up -d

# Install dependencies
docker-compose exec php-cli composer install

# Database migrations
docker-compose exec php-cli php artisan migrate:refresh

# Filing tests data
docker-compose exec php-cli php artisan db:seed

# Run tests
docker-compose exec php-cli composer test

```

Остановка сервисов

```bash
# Stop services
docker-compose up -d
```