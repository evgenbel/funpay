<?php
namespace FpDbTest\Specify;

class IntSpecify extends Specify
{
    public function getValue(): int
    {
        return isset($this->value) ? intval($this->value) : 'NULL';
    }
}
