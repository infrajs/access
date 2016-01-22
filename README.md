# Трёхуровневая система доступа test debug admin
**Disclaimer:** Module is not complete and not ready for use yet.

```php
	Access::admin(true); //exit to HTTP base authorization if fail
	(bool) Access::admin();
	(bool) Access::debug();
	(bool) Access::test();
```

В момент проверки уровня доступа при положительном ответе отправляется заголовок Cache-Control:no-store.

При проверке уровня доступа внутри кэширумой функции по дате изменения файла или другому событию созданный кэш в последствии не сбросится при положительном ответе, так как проверка возращающая положительный результат не будет запускаться и заголовка no-store не появится. Но это проблемы отладки.

Общий кэш для посетителей не будет создан если есть true при проверке уровня доступа. Кэш появится только при false. Соответственно в кэшируемом коде могут быть отладочные вызовы. Кэш будет создан только для условий false по уровню доступа, и не будет содержать отладочных вызовов или сообщений.