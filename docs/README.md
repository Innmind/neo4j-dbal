# Documentation

As this library is a lightweight wrapper of the Neo4j REST API, this documentation can only focus on 3 subjects:

* [Building a connection](#build-a-connection)
* [Querying](#querying)
* [Events](#events)

## Build a connection

To ease this process, you have access to a [`ConnectionFactory`](../ConnectionFactory.php). The process is simple:

```php
use Innmind\Neo4j\DBAL\ConnectionFactory;

$conn = ConnectionFactory::make([
    'username' => 'neo4j',
    'password' => 'neo4j',
]);
```

You now have a valid connection to your local instance of Neo4j.

**Note**: the `make` method acept a second argument which must ve an instance of the symfony `EventDispatcherInterface`. In case no dispatcher is passed to the method, it will create one and assign it to the connection (it can be retrieved afterward via `$conn->getDispatcher()`).

Below is a full list of all the (self explanatory) options (with their defaults):

```php
[
    'scheme' => 'http',
    'host' => 'localhost',
    'port' => 7474,
    'timeout' => 60,
    'username' => null, // no default
    'password' => null, // no default
]
```

---

In some cases you may want to define multiple Neo4j instances as failover. Meaning you have an instance A and B, and you want to use B automatically is A is not available. This is easily achievable with the factory above, instead of directly passing the options to build a connection, you wrap them in the `cluster` array like below:

```php
ConnectionFactory::make([
    'cluster' => [
        'A' => [
            'username' => 'foo',
            'password' => 'bar',
        ],
        'B' => [
            'username' => 'neo4j',
            'password' => 'neo4j',
        ],
    ],
]);
```

This will create a `DelegateConnection`, and at the first operation through it it will try to delegate the operation through the connection A, if not successful it will try with B (when no connection is working it will throw). In order to avoid weird states in the database, once a connection is determined at first operation, it will be used as the active one for all the other operations (meaning it will never try again to delegate through other connections it the active one fails).

At any point, you can access the currently active connection by calling:

```php
$delegateConn->getActiveConnection();
```

And you can still access any connection defined in the `cluster` option by calling:

```php
$delegateConn->getConnection($name);
```

In our case, `$name` could be either `A` or `B`.

## Querying

Here you have 2 choices, you can build the cypher by hand or via the `Query` object.

Let's start with the simpler approach, by hand:

```php
$cypher = 'MATCH (a:Foo {id: 42}) RETURN a;';
$result = $conn->execute($cypher);
```
Here you have in `$result` the data returned by the Neo4j API (the data structure is slightly different from the raw response).

If we want to be cleaner, we an use parameters in our query.

```php
$cypher = 'CREATE (a:Foo { props }) RETURN a;';
$result = $conn->execute($cypher, [
    'props' => [
        'id' => 42,
        'question' => 'What\'s the answer to life the universe and everything?'
        'anwser' => 42
    ],
]);
```
This will create a node with the label `Foo` and the properties `id`, `question` and `answer` set to the values defined in the array.

*Note*: under the hood, it uses the native capability of Neo4j parameters in order to avoir query injection.

This approah works fine if you know precisely the query you need to run. But, often, you build a query if a complex flow (i.e.: in `foreach`, `if` conditions, etc...); in such case the `Query` object is a better choice.

Example:

```php
use Innmind\Neo4j\DBAL\Query;

$query = new Query;
$query->match('(a:Foo { id: {where}.id })');
$query->addParameter('where', ['id' => 42]);

if ($someCondition) {
    $query->where('a.someProp = {someProp}');
    $query->addParameter('someProp', 'some value');
}

$query->setReturn('a');
$conn->executeQuery($query);
```

If the condition is not met, the query executed will be `MATCH (a:Foo { id: {where}.id }) RETURN a;`, otherwise it will be `MATCH (a:Foo { id: {where}.id }) WHERE a.someProp = {someProp} RETURN a;`, with respectively the parameters array set to `['where' => ['id' => 42]]` and `['where' => ['id' => 42], 'someProp' => 'some value']`.

---

A connection also allows you to run multiple queries at once like below:

```php
$conn->executeQueries([
    [
        'query' => 'some cypher',
        'parameters' => ['any' => 'value']
    ],
    //add some more queries...
]);
$conn->executeQueries([
    new Query, 
    new Query,
    //add as many you wish
]);
```
A cool thing is that you can even mix the both approches like this:

```php
$conn->executeQueries([
    new Query,
    ['query' => 'some cypher'],
    //etc...
]);
```

*Note*: when you see `new Query` above it means a query build via this object, as is it will produce an empty cypher query.

---

All the methods describe above to query the database will all return the same data structure. The library parse the raw Neo4j API response to aggregate the results in more workable way.

It looks like this:

```php
[
    'nodes' => [],
    'relationships' => [],
    'rows' => [],
    'results' => [],
]
```

`nodes` and `relationships` are arrays of all nodes and relationships returned by all the queries of a single API call. The keys of those arrays are the Neo4j ids of them.

Example:

```php
[
    'nodes' => [
        42 => [
            'id' => 42,
            'labels' => ['Foo'],
            'properties' => ['foo' => 'bar'],
        ],
    ],
    'relationships' => [
        24 => [
            'id' => 24,
            'type' => 'SOME_REL',
            'properties' => ['foo' => 'bar'],
        ],
    ],
]
```

The `rows` key contains an associative array of all the variables returned by your query.

Example, imagine you send the following query `MATCH (a)-[r]-(b) RETURN a, r, b;`, the `rows` key may look like this:

```php
[
    'rows' => [
        'a' => [
            ['foo' => 'bar'],
            ['foo' => 'baz'],
        ],
        'r' => [
            ['bar' => 42],
            ['bar' => 24],
        ],
        'b' => [
            ['some' => 'thing'],
        ],
    ],
]
```
This basically means that you have 2 nodes `a` related to a single node `b` through the 2 relationships `r`. The associatives arrays like `['foo' => 'bar']` are only the properties of the nodes/relationships (it's a restriction of the Neo4j API).

The `results` key is an array of arrays containing the 3 previous keys but separated by query.

The following code:

```php
$conn->executeQueries([
    ['query' => 'MATCH (a) RETURN a;'],
    ['query' => 'MATCH ()-[r]-() RETURN r;'],
]);
```

will return:

```php
[
    'nodes' => [/*some node from first query*/],
    'relationships' => [/*some relationships from the second query*/],
    'rows' => [
        'a' => [/*nodes properties from the first query*/],
        'r' => [/*relationship properties from the second query*/],
    ],
    'results' => [
        [
            'nodes' => [/*nodes from the first query*/],
            'relationsiphs' => [], //empty because no relationship returned by first query
            'rows' => [
                'a' => [/*nodes properties from first query*/],
            ],
        ],
        [
            'nodes' => [], //empty because no nodes returned by second query
            'relationships' => [/*relationships returned by second query*/],
            'rows' => [
                'r' => [/*relationships returned by second query*/],
            ],
        ],
    ],
]
```

## Events

The library offers 2 events to allow you intercept queries before and after an API call is made.

The event [`Events::PRE_QUERY`](../Events.php) dispatch an instance of [`PreQueryEvent`](../Event/PreQueryEvent.php). Via this event, you have access to all the `statements` sent to the Neo4j. A statement array looks like this:

```php
[
    'statement' => 'cypher query',
    'resultDataContents' => ['graph', 'row'],
    'parameters' => ['some' => 'param']
]
```

The `statements` is an array of arrays like above and can be accessed via the method `getStatements` of the event. If you wish, you can modify all the statements and inject them in the event via `setStatements` (those will be sent to the API instead of the original ones).

The second event is [`Events::POST_QUERY`](../Events.php) which dispatch an instance of [`PostQueryEvent`](../Event/PostQueryEvent.php). You have access to the statements sent to the API, the parsed json, and the guzzle response of the call; respectively through the methods `getStatements`, 'getContent' and `getResponse`.
