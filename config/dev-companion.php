<?php

if(env('APP_ENV') !== 'local' || !app()->runningInConsole()) {
    return [];
}

use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Classes\InlineCommand;

// config for WebRegulate/DevCompanion
return [
    'commands' => [
        'deploy_example' => InlineCommand::make('Deploy example', function (InlineCommand $command) {
            $command
                ->localCommand([
                    'git push origin development'
                ])->sshCommand('development', [
                    'git fetch --all',
                    'git reset --hard origin/main',
                    'composer install --no-dev --optimize-autoloader',
                    'npm run build',
                    'exit',
                ]);
        }),
        'versions' => InlineCommand::make('Check local versions', function (InlineCommand $command) {
            $command->localCommand(['php -v', 'composer -V', 'npm -v', 'node -v']);
        }),
        'ssh' => SshCommand::class,
    ],
    'ssh_connections' => [
        'development' => [
            'host' => 'XX.XX.XX.XX',
            'port' => 22,
            'user' => 'forge',
            'commands' => ['cd /home/forge/your-dev-domain.com'],
        ],
        // 'production' => [
        //     'host' => 'XX.XX.XX.XX',
        //     'port' => 22,
        //     'user' => 'forge',
        //     'commands' => ['cd /home/forge/your-production-domain.com'],
        // ],
    ],
];
