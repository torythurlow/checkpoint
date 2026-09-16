<?php

use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyProcessTimedOutException;
use Symfony\Component\Process\Process as SymfonyProcess;
use Tests\TestCase;

uses(TestCase::class);

it('reports a failed process query and exits with failure', function () {

    Process::fake([
        '*' => Process::result(exitCode: 1),
    ]);

    $this->artisan('checkpoint:processes', ['path' => 'C:\\Games\\Example\\game.exe'])
        ->expectsOutput('Windows process query failed.')
        ->assertExitCode(1);
});

it('reports a failed process query and exits with failure when there is invalid process data', function (string $output) {

    Process::fake([
        '*' => Process::result(output: $output),
    ]);

    $this->artisan('checkpoint:processes', ['path' => 'C:\\Games\\Example\\game.exe'])
        ->expectsOutput('Windows process query returned invalid data.')
        ->assertExitCode(1);
})->with([
    'malformed JSON' => 'not valid JSON',
    'non-array JSON' => 'null',
]);

it('reports a timed out process query and exits with failure', function () {
    Process::fake(function () {
        throw new SymfonyProcessTimedOutException(
            new SymfonyProcess(['powershell.exe'], timeout: 10),
            SymfonyProcessTimedOutException::TYPE_GENERAL,
        );
    });

    $this->artisan('checkpoint:processes', ['path' => 'C:\\Games\\Example\\game.exe'])
        ->expectsOutput('Windows process query timed out.')
        ->assertExitCode(1);
});
