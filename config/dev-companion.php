<?php

if(env('APP_ENV') !== 'local' || !app()->runningInConsole()) {
    return [];
}

use WebRegulate\DevCompanion\Classes\InlineCommand;
use WebRegulate\DevCompanion\Commands\AvailableCommands\SshCommand;
use WebRegulate\DevCompanion\Commands\AvailableCommands\GitUpload;

// config for WebRegulate/DevCompanion
return [
    'commands' => [
        // Deploy branch to server over SSH
        'deploy_example' => InlineCommand::make('Deploy example', function (InlineCommand $command) {
            // Get branch to SSH mappings
            $sshMappings = $command->getBranchToSshMappings();

            // Select which branch to deploy
            $branch = $command->select('Select a branch to sync', array_keys($sshMappings));

            // Execute commands
            $command
                // If main branch chosen, first merge development into main
                ->if($branch === 'main', fn($command) => $command->localCommand([
                    'git checkout main',
                    'git merge development',
                ]))
                // Push the selected branch to the remote repository
                ->localCommand([
                    "git push origin $branch",
                ])
                // Deploy the branch to the selected server
                ->sshCommand($sshMappings[$branch], [
                    'git fetch --all',
                    "git reset --hard origin/$branch",
                    'composer install --no-dev --optimize-autoloader',
                    'npm install --omit=dev',
                    'npm run build',
                    'exit',
                ])
                // If main branch, return to development branch
                ->if($branch === 'main', fn($command) => $command->localCommand([
                    'git checkout development'
                ]));
        }),

        // Check local versions
        'versions' => InlineCommand::make('Check local versions', function (InlineCommand $command) {
            $command->localCommand([
                'php -v',
                'composer -V',
                'npm -v',
                'node -v'
            ]);
        }),

        // Upload staged changes over SSH
        'git' => GitUpload::class,

        // SSH into a remote server
        'ssh' => SshCommand::class,
    ],
    'ssh_connections' => [
        'development' => [
            'branch' => 'development',
            'host' => 'XX.XX.XX.XX',
            'port' => 22,
            'user' => 'forge',
            'commands' => ['cd /home/forge/your-dev-domain.com'],
        ],
        'production' => [
            'branch' => 'main',
            'host' => 'XX.XX.XX.XX',
            'port' => 22,
            'user' => 'forge',
            'commands' => ['cd /home/forge/your-production-domain.com'],
        ],
    ],
];
