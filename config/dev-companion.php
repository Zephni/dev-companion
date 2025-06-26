<?php

use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Classes\InlineCommand;

// config for WebRegulate/DevCompanion
return [
    'available-commands' => [
        'ssh' => SshCommand::class,
        'versions' => InlineCommand::make('Check local versions [php, composer, npm, node]', function (InlineCommand $command) {
            $command->runLocalCommands(['php -v', 'composer -V', 'npm -v', 'node -v']);
        })
    ],
    'ssh_connections' => [
        'devserver' => [
            'host' => 'XX.XX.XX.XX',
            'port' => 22,
            'user' => 'forge',
            'on_connect' => 'cd /home/forge/your-dev-domain.com',
        ],
        // 'production' => [
        //     'host' => 'XX.XX.XX.XX',
        //     'port' => 22,
        //     'user' => 'forge',
        //     'on_connect' => 'cd /home/forge/your-production-domain.com',
        // ],
    ],
];
