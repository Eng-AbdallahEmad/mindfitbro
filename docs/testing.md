# Running tests

Tests run against a real MySQL database, not sqlite `:memory:` — this app
relies on MySQL-specific behavior (`ENUM` columns, unique-index NULL
semantics, `SELECT ... FOR UPDATE` row locking) that sqlite either can't
express or silently no-ops.

One-time setup, before running `php artisan test`:

```
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS mindfitbro_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

`phpunit.xml` points `DB_DATABASE` at `mindfitbro_test`. `tests/TestCase.php`
refuses to run (throws before any migration or write happens) if the active
connection's database name doesn't end in `_test` — this is a safety guard
against a misconfigured `.env`/`phpunit.xml` pointing `RefreshDatabase` at the
dev or production database.
