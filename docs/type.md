# Кастомные типы свойств

### Тип - bool
Удобный кастомный тип свойтсва для ИБ:

```php
BitrixModels\Type\BoolType
```
Добавляет тип свойства `bool` (Да / Нет) в инфоблоки.

Для подключения необходимо в `init.php` зарегистрировать событие:

```php
BitrixModels\Event\AddBoolTypeEvent::register();
```
