<?php

namespace Mhasnainjafri\APIToolkit;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mhasnainjafri\APIToolkit\Skeleton\SkeletonClass
 */
class APIToolkitFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'apitoolkit';
    }
}
