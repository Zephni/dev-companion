<?php

namespace WebRegulate\DevCompanion;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebRegulate\DevCompanion\Commands\Run;

class DevCompanionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('dev-companion')
            ->hasConfigFile()
            ->hasCommand(Run::class);
    }
}
