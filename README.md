# Vigrom. Тестовое задание 

API для работы с кошельком пользователя

[Vigrom_PHP_Test.pdf](Vigrom_PHP_Test.pdf)


В тестах используется in-memory база данных, миграции применяются при запуске теста.

Для ручной проверки необходимо накатить миграции и заполнить базу тестовыми данными. В файле [requests.http](requests.http) образцы запросов.

Запуск:

```bash
# see `make help`
make
```

Остановка

```bash
# Stop services
make stop
```