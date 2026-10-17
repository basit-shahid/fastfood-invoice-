# PostgreSQL transaction pooling

Neon's pooled endpoint uses PgBouncer transaction pooling. The PostgreSQL PDO
connection in `config/database.php` sets `PDO::PGSQL_ATTR_DISABLE_PREPARES` so
queries use unnamed statements with bound parameters. This does not disable
parameter binding or TLS.

PHP 8.2 otherwise creates session-scoped named statements. Its statement
destructor sends SQL `DEALLOCATE`, which a transaction pooler can reject after
rewriting the statement name. That error can be swallowed during cleanup; the
next query then fails with `25P02` (transaction already aborted).

The failure was reproduced with PHP 8.2 and PgBouncer 1.24 in transaction mode,
with `max_prepared_statements=200`. The same transaction and checkout tests pass
with unnamed statements enabled. Direct PostgreSQL testing alone, or testing on
PHP 8.4 with a newer libpq, can miss this failure.

Use an isolated test database behind PgBouncer to run:

```sh
php artisan test --filter='PostgresPoolingTest|OrderCheckoutTest|GuestAccessTest'
```

The checkout tests reset the test database. Never point them at production.
The pooling regression itself only runs a transaction with two SELECT queries.

Deploy the configuration change normally. No schema migration, data deletion,
or uniqueness-constraint removal is needed. If configuration is cached in an
existing deployment, run `php artisan config:clear` to load the new PDO options.
