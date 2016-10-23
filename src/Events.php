<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

final class Events
{
    const PRE_QUERY = 'innmind.neo4j.dbal.query.pre';
    const POST_QUERY = 'innmind.neo4j.dbal.query.post';
}
