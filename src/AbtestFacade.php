<?php

namespace Apurbajnu\abtest;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Apurbajnu\abtest\abtest
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
        return 'abtest';
    }
}
