<?php

namespace App\Support;

use Illuminate\Support\Facades\Process;
use JsonException;

class WindowsProcessReader
{
    /**
     * Executes a script to read windows processes and return mapped results
     *
     * @return list<array{pid: int, path: string|null}>
     *
     * @throws JsonException
     */
    public function read(): array
    {
        $script = <<<'POWERSHELL'
        $ErrorActionPreference = 'Stop'
        [Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
        $processes = @(Get-CimInstance Win32_Process | Select-Object ProcessId, ExecutablePath)
        ConvertTo-Json -InputObject $processes -Compress
        POWERSHELL;

        $command = [
            'powershell.exe',
            '-NoProfile',
            '-NonInteractive',
            '-Command',
            $script,
        ];

        $result = Process::timeout(10)->run($command);
        $result->throw();

        $processes = json_decode(
            $result->output(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (! is_array($processes)) {
            throw new \UnexpectedValueException('Process output is not an array');
        }

        $mapper = new WindowsProcessMapper;

        return $mapper->map($processes);

    }
}
