<?php

namespace Apurbajnu\Abtest;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Apurbajnu\Abtest\Abtest
 */
class AbtestFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ab-testing';
    }
}
