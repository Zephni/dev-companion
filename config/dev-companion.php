<?php

if(env('APP_ENV') !== 'local' || !app()->runningInConsole()) {
    return [];
}

use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Classes\InlineCommand;

// config for WebRegulate/DevCompanion
return [
    'commands' => [
        'example' => InlineCommand::make('Example SSH command', function (InlineCommand $command) {
            $command->sshCommand('production', ['php -v', 'composer -V', 'npm -v', 'node -v']);
        }),
        'versions' => InlineCommand::make('Check local versions', function (InlineCommand $command) {
            $command->localCommand(['php -v', 'composer -V', 'npm -v', 'node -v']);
        }),
        'ssh' => SshCommand::class,
    ],
    'ssh_connections' => [
        'devserver' => [
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
