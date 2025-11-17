# Bitrix Models - Полная документация

Библиотека для работы с данными инфоблоков, хайлоадблоков и пользователями Bitrix в стиле ООП с использованием паттерна Repository.

## Содержание

1. [Введение](#введение)
2. [Сущности (Модели)](#сущности-модели)
3. [Репозитории](#репозитории)
4. [Работа с данными](#работа-с-данными)
5. [Примеры использования](#примеры-использования)
6. [Сервисы](#сервисы)

---

## Введение

Библиотека предоставляет объектно-ориентированный подход к работе с данными Bitrix через систему сущностей (моделей) и репозиториев. Это позволяет:

- Работать с данными как с объектами
- Использовать типизацию PHP
- Применять паттерн Repository для доступа к данным
- Упростить работу с фильтрами, сортировкой и выборкой

---

## Сущности (Модели)

### BaseModel

Базовый класс для всех моделей. Содержит общую функциональность:

```php
namespace BitrixModels\Entity;

class BaseModel
{
    const IBLOCK_ID = null;    // ID инфоблока (внутреннее использование)
    const IBLOCK_CODE = null;  // Код инфоблока (обязательно для определения в моделях)
    
    public static function iblockId(): int;  // Получение ID инфоблока
    public function mapData($data): self;    // Маппинг данных в модель
    public function toArray(): array;        // Преобразование в массив
}
```

**Важно:** В каждой модели необходимо определить `IBLOCK_CODE`.

### ElementModel

Модель для работы с элементами инфоблоков.

**Поля:**
- `id` - ID элемента
- `name` - Название
- `xmlId` - Внешний код
- `active` - Активность
- `activeFrom` / `activeTo` - Даты активности
- `sort` - Сортировка
- `previewPicture` / `detailPicture` - Изображения
- `previewText` / `detailText` - Тексты
- `iblockSectionId` - ID раздела
- `detailPageUrl` - URL детальной страницы

**Свойства:**
- `properties` - Массив свойств элемента

**Пример создания:**

```php
namespace App\Entity\Content;

use BitrixModels\Entity\ElementModel;

class News extends ElementModel
{
    const IBLOCK_CODE = 'news';
}
```

**Методы:**

```php
$news = new News();
$news->getName();              // Получить название
$news->getId();                 // Получить ID
$news->getXmlId();              // Получить внешний код
$news->getPreviewText();        // Получить анонс
$news->getDetailText();         // Получить детальный текст
$news->getField('PROPERTY_CODE'); // Получить свойство
$news->toArray();               // Преобразовать в массив
```

### SectionModel

Модель для работы с разделами инфоблоков.

**Пример создания:**

```php
namespace App\Entity\Content;

use BitrixModels\Entity\SectionModel;

class NewsSection extends SectionModel
{
    const IBLOCK_CODE = 'news';
}
```

### ProductModel

Модель для работы с товарами (расширяет ElementModel).

**Дополнительные методы:**

```php
$product->getPrice();           // Получить цену
$product->getDiscount();        // Получить скидку
$product->getCatalogQuantity(); // Получить количество
$product->getCatalogWeight();   // Получить вес
```

**Пример создания:**

```php
namespace App\Entity\Catalog;

use BitrixModels\Entity\ProductModel;

class Product extends ProductModel
{
    const IBLOCK_CODE = 'catalog';
}
```

**Флаг оптимизации памяти (`optimization`):**

Для оптимизации работы с большими списками элементов можно использовать флаг `optimization`. Этот флаг доступен в `ElementModel` и `ProductModel`.

```php
// Установка флага для модели перед маппингом данных
$product = new Product();
$product->optimization = true;
$product->mapData($data);

// Или при работе с репозиторием (если нужно изменить поведение QueryBuilder)
// Обратите внимание: это нужно делать до вызова методов выборки
```

**Что делает флаг `optimization`:**
- Когда `optimization = false` (по умолчанию):
  - Сохраняются все исходные данные в `originData`
  - Сохраняются все свойства, включая пустые
  - Метод `toArray()` возвращает полные исходные данные
  
- Когда `optimization = true`:
  - Не сохраняются исходные данные (`originData` остается пустым)
  - Сохраняются только свойства с непустыми значениями (`VALUE !== null`)
  - Экономит память при работе с большими списками
  - Метод `toArray()` вернет пустой массив (так как `originData` не сохраняется)

**Рекомендации:**
- Используйте `optimization = true` для больших списков товаров, когда не нужны исходные данные
- Используйте `optimization = false` когда нужен доступ к полным данным через `toArray()`

### HighloadModel

Модель для работы с хайлоадблоками.

**Пример создания:**

```php
namespace App\Entity;

use BitrixModels\Entity\HighloadModel;

class City extends HighloadModel
{
    const IBLOCK_CODE = 'cities';
}
```

### UserModel

Модель для работы с пользователями.

**Пример создания:**

```php
namespace App\Entity;

use BitrixModels\Entity\UserModel;

class User extends UserModel
{
    // Для пользователей не требуется IBLOCK_CODE
}
```

---

## Репозитории

Репозитории предоставляют методы для работы с данными: поиск, добавление, обновление, удаление.

### BaseRepository

Базовый абстрактный класс для всех репозиториев.

**Основные методы:**

```php
abstract public function findById($id): ?BaseModel;
abstract public function findByExtId($extId): ?BaseModel;
abstract public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel;
abstract public function findByFilter(Select $select = null, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult;
abstract public function findAllByFilter(Select $select = null, Filter $filter = null, Sort $sort = null): ListResult;
abstract public function findAll(Select $select = null, Sort $sort = null): ListResult;
abstract public function countByFilter(Filter $filter = null): int;
abstract public function add(array $data = [], array $properties = []): int|false;
abstract public function update(int $id, array $data = [], array $properties = []): bool;
```

**Вспомогательные методы:**

```php
public function refresh(BaseModel &$model): BaseModel;  // Обновить модель из БД
public function getLastError(): string;                 // Получить последнюю ошибку
```

### ElementRepository

Репозиторий для работы с элементами инфоблоков.

**Пример создания:**

```php
namespace App\Repository\Content;

use App\Entity\Content\News;
use BitrixModels\Repository\ElementRepository;

class NewsRepository extends ElementRepository
{
    protected $class = News::class;
}
```

### SectionRepository

Репозиторий для работы с разделами инфоблоков.

**Пример создания:**

```php
namespace App\Repository\Content;

use App\Entity\Content\NewsSection;
use BitrixModels\Repository\SectionRepository;

class NewsSectionRepository extends SectionRepository
{
    protected $class = NewsSection::class;
}
```

### ProductRepository

Репозиторий для работы с товарами (расширяет ElementRepository).

**Пример создания:**

```php
namespace App\Repository\Catalog;

use App\Entity\Catalog\Product;
use BitrixModels\Repository\ProductRepository;

class ProductRepository extends ProductRepository
{
    protected $class = Product::class;
}
```

### HighloadRepository

Репозиторий для работы с хайлоадблоками.

**Пример создания:**

```php
namespace App\Repository;

use App\Entity\City;
use BitrixModels\Repository\HighloadRepository;

class CityRepository extends HighloadRepository
{
    protected $class = City::class;
}
```

### UserRepository

Репозиторий для работы с пользователями.

**Пример создания:**

```php
namespace App\Repository;

use App\Entity\User;
use BitrixModels\Repository\UserRepository;

class UserRepository extends UserRepository
{
    protected $class = User::class;
}
```

---

## Работа с данными

### Filter (Фильтрация)

Класс для построения фильтров запросов.

**Создание:**

```php
use BitrixModels\Model\Filter;

$filter = Filter::create();
```

**Методы фильтрации:**

```php
$filter->eq('CODE', 'test');           // Равно
$filter->neq('CODE', 'test');          // Не равно
$filter->gt('SORT', 100);              // Больше
$filter->gte('SORT', 100);             // Больше или равно
$filter->lt('SORT', 100);              // Меньше
$filter->lte('SORT', 100);             // Меньше или равно
$filter->in('ID', [1, 2, 3]);          // В массиве
$filter->notIn('ID', [1, 2, 3]);       // Не в массиве
$filter->like('NAME', '%test%');       // Похоже на
$filter->between('SORT', 10, 100);     // Между значениями
```

**Комбинирование фильтров:**

```php
$filter = Filter::create()
    ->eq('ACTIVE', 'Y')
    ->gt('SORT', 100)
    ->in('ID', [1, 2, 3]);
```

### Sort (Сортировка)

Класс для задания сортировки результатов.

**Создание:**

```php
use BitrixModels\Model\Sort;

$sort = Sort::create('SORT', 'ASC');
// или
$sort = Sort::create('ID', Sort::DESC);
```

**Методы:**

```php
$sort->setSortBy('NAME');              // Установить поле сортировки
$sort->setSortDirection(Sort::DESC);  // Установить направление
$sort->addSort('SORT', 'ASC');        // Добавить дополнительную сортировку
```

**Константы:**

```php
Sort::ASC   // По возрастанию
Sort::DESC  // По убыванию
```

### Select (Выборка полей)

Класс для управления выборкой полей и свойств.

**Создание:**

```php
use BitrixModels\Model\Select;

$select = Select::create();                    // Все поля по умолчанию
$select = Select::create(['ID', 'NAME']);      // Конкретные поля
```

**Методы:**

```php
$select->addField('CODE');                    // Добавить поле
$select->addProperty('COLOR');                // Добавить свойство
$select->withProperties();                    // Включить все свойства
$select->withAllProperties();                 // Включить все свойства и UF поля
$select->withSeo();                          // Включить SEO данные
```

**Пример:**

```php
$select = Select::create()
    ->addField('ID')
    ->addField('NAME')
    ->addProperty('COLOR')
    ->withSeo();
```

### ListResult (Результат выборки)

Класс, содержащий результат выборки с пагинацией.

**Методы:**

```php
$result->getList();              // Получить массив моделей
$result->getPagination();        // Получить объект пагинации
$result->toArray();              // Преобразовать в массив
```

**Pagination методы:**

```php
$pagination->getCurrentPage();   // Текущая страница
$pagination->getPerPage();       // Элементов на странице
$pagination->getTotalPages();    // Всего страниц
$pagination->getTotalItems();    // Всего элементов
```

---

## Примеры использования

### Поиск по ID

```php
$repository = new NewsRepository();
$news = $repository->findById(123);

if ($news) {
    echo $news->getName();
}
```

### Поиск по внешнему коду

```php
$repository = new NewsRepository();
$news = $repository->findByExtId('news-001');

if ($news) {
    echo $news->getName();
}
```

### Поиск одного элемента с фильтром

```php
use BitrixModels\Model\Filter;
use BitrixModels\Model\Sort;

$repository = new NewsRepository();

$filter = Filter::create()
    ->eq('CODE', 'test-news')
    ->eq('ACTIVE', 'Y');

$sort = Sort::create('SORT', Sort::ASC);

$news = $repository->findOneByFilter($filter, $sort);
```

### Поиск с пагинацией

```php
use BitrixModels\Model\Filter;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;

$repository = new NewsRepository();

$select = Select::create()->withAllProperties()->withSeo();
$filter = Filter::create()->eq('ACTIVE', 'Y');
$sort = Sort::create('SORT', Sort::ASC);

$result = $repository->findByFilter($select, $filter, $sort, 20, 1);

foreach ($result->getList() as $news) {
    echo $news->getName() . "\n";
}

$pagination = $result->getPagination();
echo "Страница: " . $pagination->getCurrentPage() . "\n";
echo "Всего: " . $pagination->getTotalItems() . "\n";
```

### Получение всех элементов

```php
$repository = new NewsRepository();
$result = $repository->findAll();

foreach ($result->getList() as $news) {
    echo $news->getName() . "\n";
}
```

### Подсчет элементов

```php
use BitrixModels\Model\Filter;

$repository = new NewsRepository();

$filter = Filter::create()->eq('ACTIVE', 'Y');
$count = $repository->countByFilter($filter);

echo "Активных новостей: " . $count;
```

### Добавление элемента

```php
$repository = new NewsRepository();

$data = [
    'NAME' => 'Новая новость',
    'ACTIVE' => 'Y',
    'SORT' => 500,
    'PREVIEW_TEXT' => 'Анонс новости',
    'DETAIL_TEXT' => 'Детальный текст новости',
];

$properties = [
    'COLOR' => 'red',
    'TAGS' => ['tag1', 'tag2'],
];

$id = $repository->add($data, $properties);

if ($id) {
    echo "Элемент создан с ID: " . $id;
} else {
    echo "Ошибка: " . $repository->getLastError();
}
```

### Обновление элемента

```php
$repository = new NewsRepository();

$data = [
    'NAME' => 'Обновленное название',
    'ACTIVE' => 'N',
];

$properties = [
    'COLOR' => 'blue',
];

$success = $repository->update(123, $data, $properties);

if ($success) {
    echo "Элемент обновлен";
} else {
    echo "Ошибка: " . $repository->getLastError();
}
```

### Работа с товарами (ProductModel)

```php
use App\Repository\Catalog\ProductRepository;

$repository = new ProductRepository();

$select = Select::create()->withAllProperties();
$filter = Filter::create()->eq('ACTIVE', 'Y');
$sort = Sort::create('SORT', Sort::ASC);

$result = $repository->findByFilter($select, $filter, $sort, 20, 1);

foreach ($result->getList() as $product) {
    echo $product->getName() . "\n";
    echo "Цена: " . $product->getPrice() . "\n";
    echo "Скидка: " . $product->getDiscount() . "%\n";
    echo "Количество: " . $product->getCatalogQuantity() . "\n";
}
```

### Работа с хайлоадблоками

```php
use App\Repository\CityRepository;

$repository = new CityRepository();

// Добавление
$id = $repository->add([
    'UF_NAME' => 'Москва',
    'UF_XML_ID' => 'moscow',
]);

// Поиск
$city = $repository->findByExtId('moscow');

// Обновление
$repository->update($id, [
    'UF_NAME' => 'Москва (обновлено)',
]);
```

### Работа с пользователями

```php
use App\Repository\UserRepository;

$repository = new UserRepository();

// Поиск по ID
$user = $repository->findById(1);

// Поиск с фильтром
$filter = Filter::create()->eq('ACTIVE', 'Y');
$user = $repository->findOneByFilter($filter);

// Добавление пользователя
$id = $repository->add([
    'LOGIN' => 'newuser',
    'EMAIL' => 'user@example.com',
    'NAME' => 'Иван',
    'LAST_NAME' => 'Иванов',
]);

// Обновление
$repository->update($id, [
    'NAME' => 'Петр',
]);
```

---

## Сервисы

Библиотека предоставляет набор сервисов для работы с различными типами данных.

### DateTimeService

Форматирование дат.

```php
use BitrixModels\Service\DateTimeService;

$formatted = DateTimeService::format("01.01.2024");
// Возвращает дату в формате DATE_ATOM
```

### FileService

Работа с файлами через объект `FileInfo`.

**Файловый подход (рекомендуемый):**

```php
use BitrixModels\Service\FileService;
use BitrixModels\Model\FileInfo;

// Получение информации о файле
$fileService = FileService::create();
$fileInfo = $fileService->get($fileId);

// Работа с объектом FileInfo
$fileInfo->getLink();           // Получить ссылку на файл
$fileInfo->getExtension();       // Получить расширение
$fileInfo->getFormatSize();     // Получить размер в читаемом формате (например, "1.5 MB")
$fileInfo->getSize();           // Получить размер в байтах
$fileInfo->getOriginalName();   // Получить оригинальное имя (без расширения)
$fileInfo->getId();             // Получить ID файла

// Получение списка файлов
$fileIds = [123, 456, 789];
$files = $fileService->getList($fileIds);

foreach ($files as $fileInfo) {
    echo $fileInfo->getLink() . "\n";
    echo $fileInfo->getFormatSize() . "\n";
}

// Преобразование в массив (реализует JsonSerializable)
$fileArray = $fileInfo->jsonSerialize();
```

### PictureService

Работа с изображениями.

```php
use BitrixModels\Service\PictureService;

// Стандартные размеры
PictureService::getPicture($imgId, 'small');    // 380x300
PictureService::getPicture($imgId, 'medium');    // 860x860
PictureService::getPicture($imgId, 'big');      // 1920x1920
PictureService::getPicture($imgId, 'reference'); // 100000x100000
PictureService::getPicture($imgId, '');         // 1000x1000

// Кастомный размер
PictureService::getPictureWithCustomSize($imgId, 500, 400);
```

### PhoneService

Форматирование телефонов.

```php
use BitrixModels\Service\PhoneService;

$formatted = PhoneService::format('8 (999) 000-00-00');
// Возвращает: +79990000000
```

### GeoService

Работа с геоданными.

```php
use BitrixModels\Service\GeoService;

$city = GeoService::getCityById($id);
$city = GeoService::getCityByName('Москва');
```

### UrlService

Работа с URL.

```php
use BitrixModels\Service\UrlService;

// Преобразование относительного URL в полный URL
$relativeUrl = '/news/item-123/';
$fullUrl = UrlService::getFullUrl($relativeUrl);
// Результат: "https://example.com/news/item-123/"

// Если URL уже полный, он возвращается без изменений
$fullUrl = UrlService::getFullUrl('https://example.com/news/item-123/');
// Результат: "https://example.com/news/item-123/"

// Использование с данными из моделей
$news = $repository->findById(123);
$detailUrl = UrlService::getFullUrl($news->getDetailPageUrl());
```

### SettingsService

Работа с настройками модулей.

```php
use BitrixModels\Service\SettingsService;

// Получение настроек модулей
```

---

## Обработка ошибок

Все репозитории предоставляют метод `getLastError()` для получения последней ошибки:

```php
$repository = new NewsRepository();

$id = $repository->add($data);

if (!$id) {
    $error = $repository->getLastError();
    echo "Ошибка при добавлении: " . $error;
}
```

---

## Кэширование

Библиотека автоматически использует кэширование Bitrix для оптимизации запросов. Кэш очищается автоматически при изменении данных через репозитории.

---

## Дополнительная информация

Для более детальной информации о сервисах см. [документацию по сервисам](service.md).

