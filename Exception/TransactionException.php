<?php

namespace Innmind\Neo4j\DBAL\Exception;

class TransactionException extends \Exception
{
    const OPENING_FAILED = 0;
    const COMMIT_FAILED = 1;
    const ROLLBACK_FAILED = 2;
    const QUERY_FAILURE = 3;
}
