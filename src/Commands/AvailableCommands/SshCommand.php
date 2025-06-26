<?php

namespace WebRegulate\DevCompanion\Commands\AvailableCommands;

use Illuminate\Console\Command;
use WebRegulate\DevCompanion\DevCompanion;

use function Laravel\Prompts\select;

class SshCommand extends Command
{
    public $signature = 'ssh';

    public $description = 'Access ssh shell';

    public function handle(): int
    {
        $this->comment('Accessing configured SSH Shell.');

        // If more than one SSH connection is configured, prompt the user to select one
        $sshConnections = DevCompanion::getSshConnectionKeys();

        if (count($sshConnections) > 1) {
            $selectedConnectionKey = select(
                label: 'Select SSH Connection',
                options: $sshConnections,
                scroll: 10,
            );

            if ($selectedConnectionKey === null) {
                $this->error('No SSH connection selected. Exiting.');

                return self::FAILURE;
            }
        } else {
            $selectedConnectionKey = $sshConnections[0];
        }

        // Set current SSH connection
        $currentSshConnectionConfig = DevCompanion::setCurrentSshConnection($selectedConnectionKey);

        // Connect to the SSH shell
        $host = $currentSshConnectionConfig['host'];
        $user = $currentSshConnectionConfig['user'];
        $port = $currentSshConnectionConfig['port'];
        $onConnectCommand = $currentSshConnectionConfig['on_connect'] ?? null;
        $this->line("Connecting to SSH as {$user}@{$host} on port {$port}...");

        // Set up on connect initial command
        $onConnectCommand = ! empty($onConnectCommand) ? $onConnectCommand.' ; ' : '';
        $onConnectCommand .= implode(' ; ', [
            'echo ""',
            'echo "Remote directory: $(pwd)"',
            'echo "Type exit to disconnect."',
            'exec sh',
        ]);

        // Passthru to launch a fully interactive SSH session
        $descriptorspec = [
            0 => ['file', 'php://stdin', 'r'],   // stdin
            1 => ['file', 'php://stdout', 'w'],  // stdout
            2 => ['file', 'php://stderr', 'w'],  // stderr
        ];

        $command = trim("ssh -t -p {$port} {$user}@{$host} {$onConnectCommand}");
        $process = proc_open($command, $descriptorspec, $pipes, null, null, null);

        if (is_resource($process)) {
            proc_close($process);
        }

        return self::SUCCESS;
    }
}
