<?php

namespace App\Support\Column;

class Datetime extends AbstractColumn
{

    public function renderVueValue($variable)
    {
        return '<span ' . htmlattributes(['v-date.time' => $variable]) . ' ></span>';
    }

}
