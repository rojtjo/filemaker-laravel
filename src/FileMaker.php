<?php namespace FileMaker\Laravel;

use Illuminate\Support\Facades\Facade;

class FileMaker extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filemaker';
    }

}
