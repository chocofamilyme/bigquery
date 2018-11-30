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
    'prefetchCount' => 10,

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

Для подключения к BigQuery нужно создать **json** файл и указать к нему путь под ключем `'path'`. Пример **json** файла:

```php
{
  "type": "service_account",
  "project_id": "id",
  "private_key_id": "key_id",
  "private_key": "private_key",
  "client_email": "client_email",
  "client_id": "client_id",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "cert_url"
}
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
$validator = new SenderValidator();
$streamer    = new Streamer($validator)

$mapperClass = $this->config['mappers']->get($body['table_name'], NullMapper::class);
$mapper = new $mapperClass;

$streamer->setMapper($mapper);
$streamer->validator->setClientData($data);
$streamer->send();
```

### Вставка данных с помощью задания
Используется для загрузки большого объема данных, например отчетов.
```php
$validator = new SenderValidator();
$runner    = new Runner($validator)

$mapperClass = $this->config['mappers']->get($body['table_name'], NullMapper::class);
$mapper = new $mapperClass;

$runner->setMapper($mapper);
$runner->validator->setClientData($data);
$runner->send();
```


### Пример переотправки и удаления недоставленных данных
```php
$limit = 100;

$analytics = $this->getDI()->getShared('config')->analytics;
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
$provider = new BigQuery($this->getDI()->getShared('config')->analytics);
$result = $provider->runQuery($query);
```
