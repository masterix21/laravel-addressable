<?php

namespace Masterix21\LaravelAddressable;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Masterix21\LaravelAddressable\LaravelAddressable
 */
class LaravelAddressableFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-addressable';
    }
}
