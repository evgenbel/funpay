<?php
namespace FpDbTest\Specify;

use mysqli;

class ArraySpecify extends Specify
{
    public function __construct(protected $value, protected mysqli $mysqli){
        if (!is_array($value)){
            throw new \Exception('Неверный тип аргумента');
        }
    }

    public function getValue()
    {
        if ($this->array_is_list()){
            return $this->getListValue();
        }
        return $this->getMapValue();
    }

    protected function getListValue(){
        return implode(", ",
            array_map(function($value){
                return Specify::getInstance('?', $value, $this->mysqli)->getValue();
            }, $this->value)
        );
    }

    protected function getMapValue(){
        $result = [];
        foreach ($this->value as $key => $value){
            $result[] = "`{$key}` = " . Specify::getInstance('?', $value, $this->mysqli)->getValue();
        }
        return implode(", ", $result);
    }

    protected function array_is_list()
    {
        if ($this->value === []) {
            return true;
        }
        return array_keys($this->value) === range(0, count($this->value) - 1);
    }
}
