<?php

// config for WebRegulate/DevCompanion
return [
    'available-commands' => [
        'ssh' => WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand::class,
    ],
    'ssh_connections' => [
        'production' => [
            'host' => '54.170.27.188',
            'port' => 22,
            'user' => 'forge',
            'on_connect' => 'cd /home/forge/yourmoo.deliverysoftware.co.uk',
        ],
    ],
];
