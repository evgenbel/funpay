<?php
namespace FpDbTest\Specify;

class FloatSpecify extends Specify
{
    public function getValue()
    {
        return isset($this->value) ? floatval($this->value) : 'NULL';
    }
}
