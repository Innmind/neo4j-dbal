# neo4j-dbal

| `master` | `develop` |
|----------|-----------|
|[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/build-status/master)|[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=develop) [![Build Status](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/build-status/develop)|


[![SensioLabsInsight](https://insight.sensiolabs.com/projects/47616b37-fc24-4cd2-bb0e-28fb10a55ff5/big.png)](https://insight.sensiolabs.com/projects/47616b37-fc24-4cd2-bb0e-28fb10a55ff5)

PHP abstraction layer for neo4j graph database

## Installation

Run the following command in your project to add this library:

```sh
composer require innmind/neo4j-dbal
```

## Documentation

Basic example to run a query:

```php
use Innmind\Neo4j\DBAL\{
    Query,
    Clause\Expression\Relationship
};
use Innmind\Compose\{
    ContainerBuilder\ContainerBuilder,
    Loader\Yaml
};
use Innmind\Url\Path;
use Innmind\Immutable\Map;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use Innmind\HttpTransport\GuzzleTransport;

$container = (new ContainerBuilder(new Yaml))(
    new Path('container.yml'),
    (new Map('string', 'mixed'))
        ->put('transport', new GuzzleTransport(/* arguments */))
        ->put('clock', new Earth)
);
$conn = $container->get('connection');

$query = (new Query)
    ->match('n', ['LabelA', 'LabelB'])
        ->withProperty('foo', '{param}')
        ->withParameter('param', 'value')
    ->linkedTo('n2')
    ->through('r', 'REL_TYPE', Relationship::RIGHT)
    ->return('n', 'n2', 'r');
echo (string) $query; //MATCH (n:LabelA:LabelB { foo: {param} })-[r:REL_TYPE]->(n2) RETURN n, n2, r

$result = $conn->execute($query);
echo $result->nodes()->count(); //2
echo $result->relationships()->count(); //1
```

**Note**: Each object in this library is **immutable**, so `$query->match('n')->match('n2')` is different than `$query->match('n'); $query->match('n2')`.


### Querying

You have 3 options to execute a query:

* use [`Query`](Query.php) to build the query via its API
* use [`Cypher`](Cypher.php) where you put the raw cypher query
* create your own class that implements [`QueryInterface`](QueryInterface.php)
