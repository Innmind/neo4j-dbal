# neo4j-dbal

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/neo4j-dbal/build-status/develop)

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
    ConnectionFactory,
    Query,
    Clause\Expression\Relationship
};
use Symfony\Component\EventDispatcher\EventDispatcher;

$conn = ConnectionFactory::on('localhost')
    ->for('neo4j', 'neo4j') //default neo4j credentials, you must specify them
    ->useDispatcher(new EventDispatcher) //optional

$query = (new Query)
    ->create('n', ['LabelA', 'LabelB'])
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

### Events

On each query executed 2 events are dispatched: [`Events::PRE_QUERY`](Events.php) and [`Events::POST_QUERY`](Events.php).

**Note**: in case you did not used your own dispatcher in the connection factory, you can still access the one used by the onnection via `ConnectionInterface::dispatcher()`.

The first event will give you access that will be sent to the server. The second will give you access to the query and the parsed result.
