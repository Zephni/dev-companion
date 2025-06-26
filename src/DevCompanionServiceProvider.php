<?php

namespace WebRegulate\DevCompanion;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebRegulate\DevCompanion\Commands\RunCommand;
use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;

class DevCompanionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        // Console only
        $package
            ->name('dev-companion')
            ->hasConfigFile()
            ->hasCommands(
                RunCommand::class,
                SshCommand::class,
            );
    }
}
