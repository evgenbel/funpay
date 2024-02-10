<?php
namespace FpDbTest\Specify;

use mysqli;

class IdentSpecify extends ArraySpecify
{
    public function __construct(protected $value, protected mysqli $mysqli){
        if (!is_array($value) && !is_string($value)){
            throw new \Exception('Неверный тип аргумента');
        }
    }

    public function getValue()
    {
        if (is_array($this->value)){
            if ($this->array_is_list()){
                return $this->getListValue();
            }
            return $this->getMapValue();
        }else{
            return "`" . $this->mysqli->real_escape_string($this->value) . "`";
        }
    }

    protected function getListValue(){
        return implode(", ",
            array_map(function($value){
                return "`" . $this->mysqli->real_escape_string($value) . "`";
            }, $this->value)
        );
    }
}
