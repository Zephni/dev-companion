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
                    'npm install',
                    'npm run build',
                    'exit',
                ])
                // If main branch, return to development branch
                ->if($branch === 'main', fn($command) => $command->localCommand([
                    'git checkout development'
                ]));
        }),

        // Deploy chosen branch to a selected SSH connection
        'branch' => InlineCommand::make('Deploy chosen branch to a selected SSH Connection', function (InlineCommand $command) {
            // Select which SSH connection to deploy to
            $sshConnection = $command->select('Select a SSH connection to deploy to', array_values($command->getBranchToSshMappings()));

            // Allow user to select which branch to deploy to dev
            $getAllBranches = trim(shell_exec('git branch --all --format="%(refname:short)"'));
            $branches = array_filter(explode("\n", $getAllBranches), function($branch) {
                return !str_starts_with($branch, 'remotes/')
                    && !str_starts_with($branch, 'origin/')
                    && !str_starts_with($branch, '*');
            });
            
            $branch = $command->select("Select a branch to deploy to {$sshConnection}", $branches);
            
            // Execute commands
            $command
                // Push the selected branch to the remote repository
                ->localCommand([
                    "git checkout $branch",
                    "git push origin $branch",
                ])
                // Deploy the branch to the $sshConnection server
                ->sshCommand($sshConnection, [
                    'git fetch --all',
                    "git reset --hard origin/$branch",
                    'composer install --no-dev --optimize-autoloader',
                    'npm install',
                    'npm run build',
                    'exit',
                ]); 
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
