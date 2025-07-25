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
        'deploy_example' => InlineCommand::make('Deploy example', function (InlineCommand $command) {
            // Select which branch to deploy 
            $branch = $command->select('Select a branch to sync', ['development', 'main']);

            // Set server based on branch selection
            $server = match($branch) {
                'development' => 'development',
                'main' => 'production',
            };

            // ...or instead, allow user to select which server to deploy to (If multiple configured)
            // $server = $this->selectServerKey("Select a server to deploy the `$branch` branch to");

            // Push the selected branch to the remote repository
            $command->localCommand([
                "git push origin $branch",
            ]);

            // Deploy the branch to the selected server
            $command->sshCommand($server, [
                'git fetch --all',
                "git reset --hard origin/$branch",
                'composer install --no-dev --optimize-autoloader',
                'npm install',
                'npm run build',
                'exit',
            ]);
        }),
        'versions' => InlineCommand::make('Check local versions', function (InlineCommand $command) {
            $command->localCommand([
                'php -v',
                'composer -V',
                'npm -v',
                'node -v'
            ]);
        }),
        'git' => GitUpload::class,
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
