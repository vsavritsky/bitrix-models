# bitrix-models

Библиотека для работы с данными инфоблоков, хайлоадблоков и пользователями Bitrix в стиле ООП с использованием паттерна Repository.

## Быстрый старт

### Установка

```bash
composer require your-vendor/bitrix-models
```

### Основные возможности

- ✅ Работа с элементами инфоблоков через `ElementModel` и `ElementRepository`
- ✅ Работа с разделами инфоблоков через `SectionModel` и `SectionRepository`
- ✅ Работа с товарами через `ProductModel` и `ProductRepository`
- ✅ Работа с хайлоадблоками через `HighloadModel` и `HighloadRepository`
- ✅ Работа с пользователями через `UserModel` и `UserRepository`
- ✅ Гибкая система фильтрации, сортировки и выборки данных
- ✅ Автоматическое кэширование запросов
- ✅ Набор сервисов для работы с файлами, изображениями, датами и т.д.

### Пример использования

```php
// 1. Создайте модель
namespace App\Entity\Content;

use BitrixModels\Entity\ElementModel;

class News extends ElementModel
{
    const IBLOCK_CODE = 'news';
}

// 2. Создайте репозиторий
namespace App\Repository\Content;

use App\Entity\Content\News;
use BitrixModels\Repository\ElementRepository;

class NewsRepository extends ElementRepository
{
    protected $class = News::class;
}

// 3. Используйте репозиторий
use BitrixModels\Model\Filter;
use BitrixModels\Model\Sort;
use BitrixModels\Model\Select;

$repository = new NewsRepository();

// Поиск по ID
$news = $repository->findById(123);

// Поиск с фильтром и сортировкой
$filter = Filter::create()->eq('ACTIVE', 'Y');
$sort = Sort::create('SORT', Sort::ASC);
$news = $repository->findOneByFilter($filter, $sort);

// Поиск с пагинацией
$select = Select::create()->withAllProperties()->withSeo();
$result = $repository->findByFilter($select, $filter, $sort, 20, 1);

foreach ($result->getList() as $news) {
    echo $news->getName();
}

// Добавление элемента
$id = $repository->add([
    'NAME' => 'Новая новость',
    'ACTIVE' => 'Y',
], [
    'COLOR' => 'red',
]);

// Обновление элемента
$repository->update($id, [
    'NAME' => 'Обновленное название',
]);
```

## Документация

📖 **[Полная документация](docs/README.md)** - подробное описание всех сущностей, репозиториев и примеры использования

📚 **[Документация по сервисам](docs/service.md)** - описание всех доступных сервисов

## Основные классы

### Сущности (Модели)

- `BitrixModels\Entity\ElementModel` - элементы инфоблоков
- `BitrixModels\Entity\SectionModel` - разделы инфоблоков
- `BitrixModels\Entity\ProductModel` - товары (расширяет ElementModel)
- `BitrixModels\Entity\HighloadModel` - элементы хайлоадблоков
- `BitrixModels\Entity\UserModel` - пользователи

### Репозитории

- `BitrixModels\Repository\ElementRepository` - работа с элементами
- `BitrixModels\Repository\SectionRepository` - работа с разделами
- `BitrixModels\Repository\ProductRepository` - работа с товарами
- `BitrixModels\Repository\HighloadRepository` - работа с хайлоадблоками
- `BitrixModels\Repository\UserRepository` - работа с пользователями

### Модели данных

- `BitrixModels\Model\Filter` - фильтрация запросов
- `BitrixModels\Model\Sort` - сортировка результатов
- `BitrixModels\Model\Select` - выборка полей и свойств
- `BitrixModels\Model\ListResult` - результат выборки с пагинацией

### Сервисы

- `BitrixModels\Service\DateTimeService` - форматирование дат
- `BitrixModels\Service\FileService` - работа с файлами
- `BitrixModels\Service\PictureService` - работа с изображениями
- `BitrixModels\Service\PhoneService` - форматирование телефонов
- `BitrixModels\Service\GeoService` - работа с геоданными
- `BitrixModels\Service\UrlService` - работа с URL
- `BitrixModels\Service\SettingsService` - работа с настройками

## Лицензия

См. файл [LICENSE](LICENSE)



