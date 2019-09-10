# Библиотека для работы с BigQuery


### Возможности
- Вставка данных в таблицу BigQuery
- Выполнение запросов в BigQuery

### Требования
- Phalcon 3.x+
- PHP 7.0+

### Настройка

Для работы с библиотекой нужно создать конфигурационный файл 
analytics со следующими полями

#### analytics.php
```php
...
[
    'dataset'       => 'holding',
    'path'          => STORAGE_PATH.'keys/'.env('ANALYTICS_KEYS'),
    'queueName'     => 'analytics',
    'exchangeType'  => 'direct',

    'undeliveredDataModel' => <Полное название класса для записи неотправленных данных>,

    'mappers' => [
        'table_name' => <Полное название класса для маппинга>,
    ],

    'repeater' => [
        'attempt' => env('ATTEMPT', 5),
        'exclude' => [
            \InvalidArgumentException::class,
            \Google\Cloud\Core\Exception\NotFoundException::class,
            ...
        ],
    ],
    
    'pathStorage' => STORAGE_PATH,
]
...
```

В файле `test/bootstrap.php` есть пример добавления конфига в DI.

### Пример миграции для модели UndeliveredData
```php
$table = $this->table('undelivered_data');
$table->addColumn('table_name', 'string', ['null' => false]);
$table->addColumn('data', 'text', ['null' => false]);
$table->addColumn('status', 'integer', ['default' => 0, 'limit' => 1]);
$table->addTimestamps()->create();
```

### Пример потоковой вставки данных в BigQuery
```php
$bufferSize = 50;
$validator = new SenderValidator();
$streamer    = new StreamerWrapper($validator, $bufferSize)

$mapperClass = $this->config['mappers']->get($body['table_name'], NullMapper::class);
$mapper = new $mapperClass;

$streamer->setMapper($mapper);
$streamer->validator->setClientData($data);
$streamer->send();
```

### Вставка данныых с помощью задания
Используется для загрузки большого объма данных, например отчетов.
```php
$validator = new SenderValidator();
$runner    = new RunnerWrapper($validator)

$mapperClass = $this->config['mappers']->get($body['table_name'], NullMapper::class);
$mapper = new $mapperClass;

$runner->setMapper($mapper);
$runner->validator->setClientData($data);
$runner->send();
```


### Пример переотправки и удаления недоставленных данных
```php
$limit = 100;

$analytics = $this->getDI()->getShared('config')->analytics->toArray();
$provider  = new BigQuery($analytics);

do {
    $undeliveredDataService = new UndeliveredData($analytics->undeliveredDataModel);
    $undeliveredDataSet = $undeliveredDataService->findAllUndelivered($limit);

    foreach ($undeliveredDataSet as $undeliveredData) {
        $data = \json_decode($undeliveredData->data, true);
        $bigQuery->setTable($undeliveredData->table_name);
        if ($provider->insert($data)) {
            $undeliveredData->delete();
        }
    }
} while ($undeliveredDataSet->count() >= $limit);
```

### Отправка запроса в BigQuery
Если в запросе не указать LIMIT, по умолчанию подставится LIMIT 100
```php
$query = "SELECT * FROM holding.chocolife_test WHERE created_at = \"2018-11-20\" LIMIT 100"
$provider = new BigQuery($this->getDI()->getShared('config')->analytics->toArray());
$result = $provider->runQuery($query);
```
