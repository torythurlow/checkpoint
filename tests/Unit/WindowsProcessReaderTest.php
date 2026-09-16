<?php

use App\Support\WindowsProcessReader;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

uses(TestCase::class);

it('reads Windows processes and returns a list of mapped processes', function () {

    $processes = [
        ['ProcessId' => 1234, 'ExecutablePath' => 'C:\\Games\\Example\\game.exe'],
        ['ProcessId' => 5678, 'ExecutablePath' => null],
    ];

    Process::fake([
        '*' => Process::result(
            output: json_encode($processes, JSON_THROW_ON_ERROR),
        ),
    ]);

    $reader = new WindowsProcessReader;

    $result = $reader->read();

    expect($result)->toBe([
        ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
        ['pid' => 5678, 'path' => null],
    ]);
});

it('throws when the Windows process query fails', function () {

    Process::fake([
        '*' => Process::result(
            errorOutput: 'Process query failed',
            exitCode: 1,
        ),
    ]);

    $reader = new WindowsProcessReader;

    expect(fn () => $reader->read())->toThrow(ProcessFailedException::class);
});

it('throws when process output is invalid JSON', function () {

    Process::fake([
        '*' => Process::result(output: 'not valid JSON'),
    ]);

    $reader = new WindowsProcessReader;

    expect(fn () => $reader->read())->toThrow(JsonException::class);
});

it('rejects process output that is not an array', function () {

    Process::fake([
        '*' => Process::result(output: 'null'),
    ]);

    $reader = new WindowsProcessReader;

    expect(fn () => $reader->read())->toThrow(UnexpectedValueException::class);
});
