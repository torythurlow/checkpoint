<?php

use App\Support\WindowsProcessMapper;

it('maps Windows process fields and preserves null paths', function () {

    $processes = [
        ['ProcessId' => 1234, 'ExecutablePath' => 'C:\\Games\\Example\\game.exe'],
        ['ProcessId' => 5678, 'ExecutablePath' => null],
    ];

    $mapper = new WindowsProcessMapper;

    $result = $mapper->map($processes);

    expect($result)->toBe([
        ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
        ['pid' => 5678, 'path' => null],
    ]);

});

it('maps Windows process fields and returns and empty array for empty input', function () {
    $processes = [];

    $mapper = new WindowsProcessMapper;

    $result = $mapper->map($processes);

    expect($result)->toBe([]);
});

it('maps decoded Windows process JSON', function () {
    $json = '[{"ProcessId":1234,"ExecutablePath":null}]';

    $processes = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

    $mapper = new WindowsProcessMapper;

    $result = $mapper->map($processes);

    expect($result)->toBe([['pid' => 1234, 'path' => null]]);
});
