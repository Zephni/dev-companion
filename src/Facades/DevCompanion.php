<?php

namespace WebRegulate\DevCompanion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WebRegulate\DevCompanion\DevCompanion
 */
class DevCompanion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \WebRegulate\DevCompanion\DevCompanion::class;
    }
}
