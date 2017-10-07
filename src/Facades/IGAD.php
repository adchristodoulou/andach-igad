<?php
namespace Andach\IGAD\Facades;
use Illuminate\Support\Facades\Facade;
class IGAD extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'igad'; }
}