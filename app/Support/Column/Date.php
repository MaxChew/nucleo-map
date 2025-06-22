<?php

namespace App\Support\Column;

class Date extends AbstractColumn
{

    public function renderVueValue($variable)
    {
        return '<span ' . htmlattributes(['v-date' => $variable]) . ' ></span>';
    }

}
