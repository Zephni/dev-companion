<?php

namespace WebRegulate\DevCompanion;

class DevCompanion
{
    public static string $currentSshConnectionKey = '';

    public static function setCurrentSshConnection(string $connectionKey): array
    {
        $sshConnections = self::getSshConnections();

        if (! array_key_exists($connectionKey, $sshConnections)) {
            throw new \InvalidArgumentException("SSH connection key '$connectionKey' does not exist.");
        }

        self::$currentSshConnectionKey = $connectionKey;

        return $sshConnections[$connectionKey];
    }

    public static function getSshConnections(): array
    {
        return config('dev-companion.ssh_connections', []);
    }

    public static function getSshConnectionKeys(): array
    {
        return array_keys(self::getSshConnections());
    }

    public static function getCurrentSshConnectionKey(): string
    {
        return self::$currentSshConnectionKey;
    }

    public static function getCurrentConnectionConfig(): array
    {
        $connectionKey = self::getCurrentSshConnectionKey();
        if (empty($connectionKey)) {
            throw new \RuntimeException('No SSH connection is currently set.');
        }

        // Loop over values and escape them
        $connectionConfig = self::getSshConnections()[$connectionKey] ?? [];
        foreach ($connectionConfig as $key => $value) {
            $connectionConfig[$key] = escapeshellarg($value);
        }

        return $connectionConfig;
    }
}
