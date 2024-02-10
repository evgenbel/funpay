<?php
namespace FpDbTest\Specify;

use mysqli;

class StringSpecify extends Specify
{
    public function __construct(protected $value, protected mysqli $mysqli){
        if (!is_string($value)){
            throw new \Exception('Неверный тип аргумента');
        }
    }

    public function getValue(): string
    {
        return isset($this->value) ? "'" . $this->mysqli->real_escape_string($this->value) . "'" : 'NULL';
    }
}
