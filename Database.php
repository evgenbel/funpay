<?php

namespace FpDbTest;

use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $replace = new Replace();
        return $replace
            ->setTemplate($query)
            ->setArgs($args)
            ->setMysql($this->mysqli)
            ->getQuery();
    }

    public function skip()
    {
        return Replace::SKIP;
    }
}
