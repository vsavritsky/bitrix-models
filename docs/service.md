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

Вспомогательные методы для работы с файлами

```php
use BitrixModels\Service\FileService;

FileService::getLink($fileId);
FileService::getExtension($fileId);
FileService::getFormatSize($fileId);
FileService::getSize($fileId);
FileService::getOriginalName($fileId);
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

Сервис для работы с изображениями

позволяет сжимать изображения до нужного формата/размера
есть дефолтные форматы:

`$size = 'small' - ['width' => 380, 'height' => 300]`

`$size = 'medium' - ['width' => 860, 'height' => 860]`

`$size = 'big' - ['width' => 1920, 'height' => 1920]`

`$size = 'reference' - ['width' => 100000, 'height' => 100000]`

`$size = '' - ['width' => 1000, 'height' => 1000]`



```php
use BitrixModels\Service\PictureService;

PictureService::getPicture($imgId, $size);
```

или же можно вызвать метод
`getPictureWithCustomSize` указав свои параметры высоты и ширины
```php
PictureService::getPictureWithCustomSize($imgId, $width, $height);
```