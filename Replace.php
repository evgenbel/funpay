<?php

namespace FpDbTest;

use FpDbTest\Specify\Specify;
use mysqli;

class Replace
{
    const SKIP = '\u0008';
    private string $template;
    private array $args;
    private mysqli $mysqli;

    public function setTemplate(string $template): Replace
    {
        $this->template = $template;
        return $this;
    }

    public function setArgs(array &$args): Replace
    {
        $this->args = $args;
        return $this;
    }

    public function setMysql(mysqli $mysqli): Replace
    {
        $this->mysqli = $mysqli;
        return $this;
    }

    public function getQuery(): string
    {
        $replacedItems = $this->findReplacedItems($this->template);
        $result = $this->replaceItems($this->template, $replacedItems);
        if (!empty($this->args))
            throw new \Exception("Некорректное количество аргументов");
        return $result;
    }


    private function replaceItems(string $template, array $items, $if_block = false)
    {
        $skipped = false;
        foreach ($items as $item) {
            if ($skipped) {//блок пропускается, надо только выдернуть все аргументы блока
                array_shift($this->args);
                continue;
            }

            if ($this->isIfBlock($item)) {
                $value = $this->replaceBlock($item);
            } else {
                if (empty($this->args))
                    throw new \Exception("Некорректное количество аргументов");
                $arg = array_shift($this->args);
                $value = '';
                if ($if_block && $arg === self::SKIP) {
                    //Это условный блок и нашлось значение пропуска
                    //надо будет после обработки всех значение вернуть пусто,
                    // т.е. не включать блок в итоговый результат
                    $skipped = true;
                    continue;
                } else {
                    $value = $this->replaceSpecificator($item, $arg);
                }
            }
            $template = $this->replace($template, $item, $value);
        }
        return $skipped ? '' : $template;
    }

    private function replace(string $template, string $block, mixed $value): string
    {
        $pos = strpos($template, $block);
        if ($pos !== false) {
            $template = substr_replace($template, $value, $pos, strlen($block));
        }
        return $template;
    }

    private function replaceSpecificator(string $specify, mixed $arg): mixed
    {
        return Specify::getInstance($specify, $arg, $this->mysqli)->getValue();
    }

    private function replaceBlock(string $block): string
    {
        $innerBlock = $this->removeCurles($block);
        $foundedInnerBlocks = $this->findReplacedItems($innerBlock);
        return $this->replaceItems($innerBlock, $foundedInnerBlocks, true);
    }

    private function removeCurles(string $block): string
    {
        return substr($block, 1, -1);
    }

    private function findReplacedItems($query): array
    {
        preg_match_all('/((\?[dfa#]?)|({.*?}))/', $query, $matches);
        return $matches[0];
    }

    private function isIfBlock(string $item): bool
    {
        return substr($item, 0, 1) == "{" && substr($item, -1, 1) == "}";
    }
}
