<?php

namespace Maxxidev\Author;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Maxxidev\Author\Blueprint as AuthorBlueprint;

class ServiceProvider extends LaravelServiceProvider
{
    public function register()
    {
        Blueprint::mixin(new AuthorBlueprint);
    }
}
