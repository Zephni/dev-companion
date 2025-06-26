<?php

// config for WebRegulate/DevCompanion
return [
    'available-commands' => [
        'ssh' => WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand::class,
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
