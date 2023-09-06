# bitrix-models

Библиотека предлагает работать с данные из инфоблоков, хайлоадов битрикса в стиле ООП (в частности с сущностями, репозиториями и прочее)

Основные классы сущностей
```
BitrixModels\Entity\ElementModel - класс для элементов инфоблока
BitrixModels\Entity\SectionModel - класс для разделов инфоблоков 
BitrixModels\Entity\HighloadModel - класс для элементов хайлоадблока
BitrixModels\Entity\ProductModel - класс для товаров и тп
BitrixModels\Entity\UserModel - класс для пользователей
```

Классы репозиториев
```
BitrixModels\Repository\ElementRepository - класс для выборки элементов инфоблока
BitrixModels\Repository\SectionRepository - класс для выборки разделов инфоблоков 
BitrixModels\Repository\HighloadRepository - класс для выборки элементов хайлоадблока
BitrixModels\Repository\ProductRepository - класс для выборки товаров и тп
BitrixModels\Repository\UserRepository - класс для выборки пользователей
```

Хелперы
```
BitrixModels\Service\DateTimeService - класс для форматирования даты
BitrixModels\Service\FileService - класс для получения ссылки на файл
BitrixModels\Service\PictureService - класс для быстрого сжатия изображений
```

```
// Пример создания сущности новость

namespace App\Entity\Content;

use BitrixModels\Entity\ElementModel;

class News extends ElementModel
{
    /**
     * @var int
     */
    const IBLOCK_CODE = 'news'; 
}

Пример создания сущности репозитория для новостей

namespace App\Repository\Content;

use App\Entity\Content\News;

class NewsRepository extends \BitrixModels\Repository\ElementRepository
{
    protected $class = News::class;
}

// получение одного элемента по фильтру с сортировкой
$repository = new App\Repository\Content\NewsRepository();
$repository->findOneByFilter(Filter::create()->eq('CODE', 'TEST'), Sort::create('SORT', 'DESC'));

// получение списка с пагинацией по фильтру
$repository = new App\Repository\Content\NewsRepository();
$result = $repository->findByFilter(Select::create()->withProperties(), Filter::create()->eq('CODE', 'TEST'), Sort::create('SORT', 'DESC'), 1, 20);
foreach($result->getList() as $item) {
  
}
$pagination = $result->getPagination();

```



