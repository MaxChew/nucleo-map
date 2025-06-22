<?php

namespace App\Support\Column;
use Illuminate\Support\Str;

abstract class AbstractColumn
{
    public $type;
    public $name;
    public $label;
    public $align = 'left';
    public $sortable;
    public $width;
    public $attributes = [];

    public function __construct($name, array $options = [])
    {
        $this->name = $name;
        $this->label = $this->getDefaultLabel();
        foreach($options as $option => $value){
            $this->$option = $value;
        }
        $this->type = Str::of(class_basename($this))->snake();
    }

    protected function getDefaultLabel()
    {
        return start_case(str_replace('.', ' ', $this->name));
    }

    public function renderVueValue($variable)
    {
        return '<span ' . htmlattributes(['v-text' => $variable]) . ' ></span>';
    }

}