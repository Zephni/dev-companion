<?php

namespace WebRegulate\DevCompanion;

use Spatie\LaravelPackageTools\Package;
use WebRegulate\DevCompanion\Commands\RunCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Commands\AvailableCommands\LocalCommand;

class DevCompanionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

         // Spatie package requires a name even if we don't use it
        $package
            ->name('dev-companion');

        // Local console only
        if(!app()->isLocal() || !app()->runningInConsole()) {
            return;
        }

        // Package configuration
        $package
            ->hasConfigFile()
            ->hasConsoleCommands(
                RunCommand::class,
                LocalCommand::class,
                SshCommand::class
            )
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile();
            });
    }
}
