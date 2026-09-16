<?php

namespace App\Support;

class WindowsProcessMapper
{
    /**
     * Map Windows process fields to Checkpoint's process structure, preserving unavailable paths as null.
     *
     * @param  array<int, array{ProcessId: int, ExecutablePath: string|null}>  $processes
     * @return list<array{pid: int, path: string|null}>
     */
    public function map(array $processes): array
    {
        $results = [];
        foreach ($processes as $process) {
            $results[] = [
                'pid' => $process['ProcessId'],
                'path' => $process['ExecutablePath'],
            ];
        }

        return $results;
    }
}
