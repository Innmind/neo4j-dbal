# neo4j-dbal

[![Build Status](https://github.com/Innmind/neo4j-dbal/workflows/CI/badge.svg)](https://github.com/Innmind/neo4j-dbal/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/Innmind/neo4j-dbal/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/neo4j-dbal)
[![Type Coverage](https://shepherd.dev/github/Innmind/neo4j-dbal/coverage.svg)](https://shepherd.dev/github/Innmind/neo4j-dbal)


PHP abstraction layer for neo4j graph database

## Installation

Run the following command in your project to add this library:

```sh
composer require innmind/neo4j-dbal
```

## Documentation

Basic example to run a query:

```php
use function Innmind\Neo4j\DBAL\bootstrap;
use Innmind\Neo4j\DBAL\{
    Query,
    Clause\Expression\Relationship
};
use Innmind\TimeContinuum\TimeContinuum\Earth;
use function Innmind\HttpTransport\bootstrap as transports;

$connection = bootstrap(
    transports()['default'](),
    new Earth
);

$query = (new Query)
    ->match('n', ['LabelA', 'LabelB'])
        ->withProperty('foo', '{param}')
        ->withParameter('param', 'value')
    ->linkedTo('n2')
    ->through('r', 'REL_TYPE', 'right')
    ->return('n', 'n2', 'r');
echo (string) $query; //MATCH (n:LabelA:LabelB { foo: {param} })-[r:REL_TYPE]->(n2) RETURN n, n2, r

$result = $connection->execute($query);
echo $result->nodes()->count(); //2
echo $result->relationships()->count(); //1
```

**Note**: Each object in this library is **immutable**, so `$query->match('n')->match('n2')` is different than `$query->match('n'); $query->match('n2')`.


### Querying

You have 3 options to execute a query:

* use [`Query`](Query.php) to build the query via its API
* use [`Cypher`](Cypher.php) where you put the raw cypher query
* create your own class that implements [`QueryInterface`](QueryInterface.php)

## Structure

![](graph.svg)
