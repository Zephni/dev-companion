<?php

namespace WebRegulate\DevCompanion\Commands\AvailableCommands;

use WebRegulate\DevCompanion\DevCompanion;
use WebRegulate\DevCompanion\Classes\BaseCommand;

class GitUpload extends BaseCommand
{
    public $signature = 'dev-companion:git-upload {connection_key?}';

    public $description = 'Uploads staged git files to a remote server via SSH.';

    public static $filename = 'dev-companion.staged-git-files.txt';

    public function handle(): int
    {
        try {
            // Get passed arguments
            $connectionKey = $this->argument('connection_key');
    
            // Display a message to the user
            $this->comment('Accessing configured SSH Shell.');
    
            // If connection key is provided, use it directly
            if(!empty($connectionKey)) {
                $selectedConnectionKey = $connectionKey;
            }
            // Otherwise, either select from configured connections or use the first one if only one exists
            else {
                // If more than one SSH connection is configured, prompt the user to select one
                $sshConnections = DevCompanion::getSshConnectionKeys();
        
                if (count($sshConnections) > 1) {
                    $selectedConnectionKey = $this->select(
                        label: 'Select SSH Connection',
                        options: $sshConnections,
                        scroll: 10,
                    );
        
                    if ($selectedConnectionKey === null) {
                        $this->error('No SSH connection selected. Exiting.');
                        return $this->cleanupAndReturnResult(self::FAILURE);
                    }
                } else {
                    $selectedConnectionKey = $sshConnections[0];
                }
            }
    
            // Connectioon
            $this->line("Using connection key: {$selectedConnectionKey}");
    
            // Set current SSH connection
            $currentSshConnectionConfig = DevCompanion::setCurrentSshConnection($selectedConnectionKey);
    
            // Connect to the SSH shell
            $host = $currentSshConnectionConfig['host'];
            $user = $currentSshConnectionConfig['user'];
            $port = $currentSshConnectionConfig['port'];
            $basePath = $currentSshConnectionConfig['base_path'] ?? '';
            $this->line("Using connection and path {$user}@{$host}:{$basePath} on port {$port}...");
    
            DevCompanion::$displayCommands = false;
            
            // Delete any existing staged git files file
            if (file_exists(static::$filename)) unlink(static::$filename);
    
            // Get staged git files
            exec('git diff --cached --name-only > '.static::$filename, $output, $exitCode);
    
            // Exit if there was an error getting staged git files
            if ($exitCode !== 0) {
                $this->error('Failed to get staged git files. Reason: ' . implode("\n", $output));
                return $this->cleanupAndReturnResult(self::FAILURE);
            }
    
            // Read the staged git files from the file into an array
            $stagedGitFiles = preg_split('/\r\n|\n|\r/', file_get_contents(static::$filename) ?? '');
            $stagedGitFiles = array_filter($stagedGitFiles, fn($file) => trim($file) !== '');
    
            // If no staged files, notify the user and exit
            if(empty($stagedGitFiles)) {
                $this->line('<info>No files staged for commit.</info>');
                return $this->cleanupAndReturnResult(self::SUCCESS);
            }
    
            $this->line('<info>Files staged for commit:</info>');
    
            foreach ($stagedGitFiles as $file) {
                $this->line("<comment>$file</comment>");
            }
    
            // Ask the user if they want to upload the staged files
            if (strtolower($this->askWithCompletion('Do you want to upload these files?', ['y', 'n'], 'y')) === 'y') {
                // Get the local directory path
                $localDir = base_path();
    
                // Loop through each staged file and upload it
                foreach ($stagedGitFiles as $file) {
                    // Build variables for use in the final upload command
                    $connectionAndBasePath = "{$user}@{$host}:{$basePath}";
                    $localFilePath = "$localDir/$file";
                    $fullRemotePath = "$connectionAndBasePath/$file";
                    $rsyncCommand = "scp -P {$port} $localFilePath $fullRemotePath";
                    $rsyncCommand = str_replace('//', '/', $rsyncCommand);
                    $rsyncCommand = str_replace(["\n", "\r"], '', $rsyncCommand);
    
                    // Notify the user we are in the process of uploading
                    $this->line("<info>Uploading:</info> <comment>$file</comment> to <comment>$fullRemotePath</comment>");
                    
                    // Execute the upload command
                    exec($rsyncCommand, $output, $exitCode);
    
                    // If the upload failed, notify the user and continue to the next file
                    if ($exitCode !== 0) {
                        $this->error("Failed to upload '$file', message: " . implode("\n", $output));
                        continue;
                    }
    
                    // Notify the user that the file was successfully uploaded
                    $this->line("<info>✅ Uploaded successfully</info>");
                    $this->newLine();
                }
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return $this->cleanupAndReturnResult(self::FAILURE);
        }

        return $this->cleanupAndReturnResult(self::SUCCESS);
    }

    private function cleanupAndReturnResult(int $result): int
    {
        // Remove the staged git files file
        if (file_exists(static::$filename)) {
            unlink(static::$filename);
        }

        // Return result
        return $result;
    }
}
