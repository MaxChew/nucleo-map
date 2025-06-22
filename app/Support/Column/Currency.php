<?php

namespace App\Support\Column;

class Currency extends AbstractColumn
{
    public $align = 'right';

    public function renderVueValue($variable)
    {
        return '<span class="whitespace-no-wrap" ' . htmlattributes(['v-text' => "\$currency.format($variable)"]) . ' ></span>';
    }
}
