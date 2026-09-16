<?php

namespace App\Console\Commands;

use App\Support\ExecutableMatcher;
use App\Support\WindowsProcessReader;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use JsonException;

#[Signature('checkpoint:processes {path}')]
#[Description('Detects currently open windows processes.')]
class DetectProcesses extends Command
{
    /**
     * Execute the console command.
     *
     * @throws JsonException
     */
    public function handle(): int
    {
        $reader = new WindowsProcessReader;
        $matcher = new ExecutableMatcher;
        $path = $this->argument('path');

        for ($i = 0; $i < 5; $i++) {
            $startedAt = microtime(true);

            try {
                $results = $reader->read();
            } catch (ProcessTimedOutException $e) {
                $this->error('Windows process query timed out.');

                return self::FAILURE;
            } catch (ProcessFailedException $e) {
                $this->error('Windows process query failed.');

                return self::FAILURE;
            } catch (JsonException|\UnexpectedValueException $e) {
                $this->error('Windows process query returned invalid data.');

                return self::FAILURE;
            }

            $durationMs = (microtime(true) - $startedAt) * 1000;

            $matchingIds = $matcher->matchingProcessIds($results, $path);

            if (empty($matchingIds)) {
                $this->info('No matching processes found.');
            } else {
                $this->info('Matching Process IDs: '.implode(', ', $matchingIds));
            }
            $this->info('Read duration: '.round($durationMs, 1).' ms');

            if ($i < 4) {
                sleep(2);
            }

        }

        return self::SUCCESS;
    }
}
