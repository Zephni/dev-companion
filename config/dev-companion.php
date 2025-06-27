<?php

if(env('APP_ENV') !== 'local') {
    return [];
}

use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Classes\InlineCommand;

// config for WebRegulate/DevCompanion
return [
    'available-commands' => [
        '0' => InlineCommand::make('Example SSH command', function (InlineCommand $command) {
            $command->callSshCommand('production', [
                'php -v',
                'node -v',
            ]);
        }),
        'ssh' => SshCommand::class,
        'versions' => InlineCommand::make('Check local versions', function (InlineCommand $command) {
            $command->runLocalCommands(['php -v', 'composer -V', 'npm -v', 'node -v']);
        }),
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
