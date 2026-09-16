<?php

namespace App\Support;

class ExecutableMatcher
{
    /**
     * Return IDs of processes whose executable paths match the target,
     * ignoring letter casing and skipping unavailable paths.
     *
     * @param  array<int, array{pid: int, path: string|null}>  $processes
     * @return list<int>
     */
    public function matchingProcessIds(array $processes, string $targetPath): array
    {
        $processIds = [];
        foreach ($processes as $process) {

            if ($process['path'] === null) {
                continue;
            }

            if (strcasecmp($process['path'], $targetPath) === 0) {
                $processIds[] = $process['pid'];
            }
        }

        return $processIds;
    }
}
