<?php

namespace App\Support\Column;

class Number extends AbstractColumn
{
    public $align = 'right';
    public $precision;

    public function renderVueValue($variable)
    {
    	if ($this->precision !== null) {
        	return '<span class="whitespace-no-wrap" ' . htmlattributes(['v-text' => "\$number.format($variable, $this->precision)"]) . ' ></span>';
    	}
        return '<span class="whitespace-no-wrap" ' . htmlattributes(['v-text' => "\$number.format($variable)"]) . ' ></span>';
    }
}
