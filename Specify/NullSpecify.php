<?php
namespace FpDbTest\Specify;

class NullSpecify extends Specify
{
    public function getValue()
    {
        return 'NULL';
    }
}
