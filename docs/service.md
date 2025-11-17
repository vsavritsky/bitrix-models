# Сервисы

### DateTimeService

Возвращает дату в формате [DATE_ATOM](https://www.php.net/manual/ru/datetime.constants.php#constant.date-atom).
Пример использования:

```php
use BitrixModels\Service\DateTimeService;

DateTimeService::format("01.01.2024");
```


### EnvService

Позволяет получить значение параметров модулей, так же как 

```php
Bitrix\Main\Config\Option::get($code, $module)
```

Дополнително проверяет наличие этого параметра в глобальном массиве `$_ENV[$code]`


### FileService

Работа с файлами через объект `FileInfo`.

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

### GeoService

Сервис содржит в себе два метода 
`getCityById`  - для получения информации о городе по его ID,
`getCityByName`  - для получения информации о городе по его Названию,

```php
use BitrixModels\Service\GeoService;

GeoService::getCityById($id);
GeoService::getCityByName($name);
```

### PhoneService

Сервис для работы номером телефона.
форматирует и приводит телефон к виду: 
`+7XXXXXXXXXX`

```php
use BitrixModels\Service\PhoneService;

PhoneService::format('8 (999) 000-00-00');
```

### PictureService

Сервис для работы с изображениями. Позволяет сжимать изображения до нужного формата/размера.

**Дефолтные форматы (константы):**

- `PictureService::SIZE_SMALL` - 380x300
- `PictureService::SIZE_MEDIUM` - 860x860
- `PictureService::SIZE_BIG` - 1920x1920
- `PictureService::SIZE_REFERENCE` - 100000x100000 (без сжатия)

**Базовое использование:**

```php
use BitrixModels\Service\PictureService;

$pictureService = PictureService::create();

// Получение изображения с дефолтным размером (small)
$imageUrl = $pictureService->get($imgId);

// Использование констант размеров
$imageUrl = $pictureService->get($imgId, PictureService::SIZE_MEDIUM);
$imageUrl = $pictureService->get($imgId, PictureService::SIZE_BIG);

// Получение полного URL
$fullUrl = $pictureService->get($imgId, PictureService::SIZE_MEDIUM, true);
```

**Кастомный размер:**

```php
// Получение изображения с произвольными размерами
$imageUrl = $pictureService->getPictureWithCustomSize($imgId, 500, 400);

// С настройкой сжатия (0-100, по умолчанию 80)
$imageUrl = $pictureService->getPictureWithCustomSize($imgId, 500, 400, 90);

// С полным URL
$fullUrl = $pictureService->getPictureWithCustomSize($imgId, 500, 400, 80, true);
```

**Работа с водяным знаком:**

```php
// Настройка водяного знака
$pictureService = PictureService::create();
$pictureService->setWatermark('/upload/watermark.png');
$pictureService->setWatermarkPosition('center'); // Позиция: center, top, bottom, left, right
$pictureService->setWatermarkSize('medium');     // Размер: small, medium, big

// Получение изображения с водяным знаком
$imageUrl = $pictureService->getWithWatermark($imgId, PictureService::SIZE_MEDIUM);
```

**Настройка сжатия:**

```php
$pictureService = PictureService::create();
$pictureService->setCompression(90); // Качество сжатия от 0 до 100

$imageUrl = $pictureService->get($imgId, PictureService::SIZE_MEDIUM);
```

**Получение списка изображений:**

```php
$pictureService = PictureService::create();

// Список изображений с одним размером
$imageIds = [123, 456, 789];
$imageUrls = $pictureService->getList($imageIds, PictureService::SIZE_MEDIUM);

foreach ($imageUrls as $url) {
    echo $url . "\n";
}

// Список изображений с водяным знаком
$imageUrls = $pictureService->getListWithWatermark($imageIds, PictureService::SIZE_MEDIUM);
```

**Добавление кастомного размера:**

```php
$pictureService = PictureService::create();
$pictureService->setSize('custom', 1200, 800);

// Использование кастомного размера
$imageUrl = $pictureService->get($imgId, 'custom');
```

**Пример использования с моделями:**

```php
use BitrixModels\Service\PictureService;

$news = $repository->findById(123);
$previewPictureId = $news->getPreviewPicture();

if ($previewPictureId) {
    $pictureService = PictureService::create();
    $previewUrl = $pictureService->get($previewPictureId, PictureService::SIZE_MEDIUM);
    $detailUrl = $pictureService->get($news->getDetailPicture(), PictureService::SIZE_BIG);
}
```