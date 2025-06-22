<?php

namespace App\Support\Column;

class Html extends AbstractColumn
{

    public function renderVueValue($variable)
    {
        return '<span ' . htmlattributes(['v-html' => $variable]) . ' ></span>';
    }

}
