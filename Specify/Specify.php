<?php
namespace FpDbTest\Specify;

use mysqli;

abstract class Specify
{
    CONST TYPE_INT = 'd';
    CONST TYPE_FLOAT = 'f';
    CONST TYPE_ARRAY = 'a';
    CONST TYPE_IDENT = '#';
    CONST TYPE_NULL = '';
    CONST TYPE_STRING = 's';

    static function getInstance(string $spec, $value, mysqli $mysqli = null): Specify
    {
        $type = self::getType($spec, $value);
        return self::getClass($type, $value, $mysqli);
    }

    public function __construct(protected $value){

    }

    public abstract function getValue();

    private static function getClass($type, $value, mysqli $mysqli): Specify
    {
        switch($type){
            case self::TYPE_INT:
                return new IntSpecify($value);
            case self::TYPE_FLOAT:
                return new FloatSpecify($value);
            case self::TYPE_ARRAY:
                return new ArraySpecify($value, $mysqli);
            case self::TYPE_IDENT:
                return new IdentSpecify($value, $mysqli);
            case self::TYPE_NULL:
                return new NullSpecify($value);
            case self::TYPE_STRING:
                return new StringSpecify($value, $mysqli);
        }
        throw new \Exception('Неподдерживаемый тип спецификатора');
    }

    private static function getType(string $spec, $value){
        if ($spec == '?'){
            return self::getTypeValue($value);
        }
        switch($spec){
            case "?d":
                return self::TYPE_INT;
            case "?f":
                return self::TYPE_FLOAT;
            case "?a":
                return self::TYPE_ARRAY;
            case "?#":
                return self::TYPE_IDENT;
        }
        throw new \Exception('Неизвестный спецификатор');
    }

    private static function getTypeValue($value){
        if (!isset($value)){
            return self::TYPE_NULL;
        }
        if (is_int($value)){
            return self::TYPE_INT;
        }
        if (is_bool($value)){
            return self::TYPE_INT;
        }
        if (is_float($value)){
            return self::TYPE_FLOAT;
        }
        if (is_array($value)){
            return self::TYPE_ARRAY;
        }
        if (is_string($value)){
            return self::TYPE_STRING;
        }
        throw new \Exception('Неподдерживаемый тип аргумента');
    }
}
