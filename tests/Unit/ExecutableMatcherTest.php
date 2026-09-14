<?php

use App\Support\ExecutableMatcher;

it('Matches executables with process ids', function () {
    $processes = [
        ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
        ['pid' => 1235, 'path' => 'C:\\Other\\game.exe'],
    ];

    $matcher = new ExecutableMatcher;

    $matchingExecutable = $matcher->matchingProcessIds($processes, 'C:\\Games\\Example\\game.exe');

    expect($matchingExecutable)->toBe([1234]);
});

it('Matches executables with process ids and skips over a null path successfully', function () {
    $processes = [
        ['pid' => 1234, 'path' => null],
        ['pid' => 1235, 'path' => 'C:\\Games\\Example\\game.exe'],
    ];

    $matcher = new ExecutableMatcher;

    $matchingExecutable = $matcher->matchingProcessIds($processes, 'C:\\Games\\Example\\game.exe');

    expect($matchingExecutable)->toBe([1235]);
});

it ('Returns an empty array when it cannot find a matching executable path', function () {
   $processes = [
       ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
   ];

   $matcher = new ExecutableMatcher;

   $matchingExecutable = $matcher->matchingProcessIds($processes, 'C:\\Games\\Example\\nonexistentgame.exe');

   expect($matchingExecutable)->toBe([]);
});

it('Matches multiple PIDs to the same executable ', function () {
    $processes = [
        ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
        ['pid' => 1235, 'path' => 'C:\\Games\\Example\\game.exe'],
    ];

    $matcher = new ExecutableMatcher;

    $matchingExecutable = $matcher->matchingProcessIds($processes, 'C:\\Games\\Example\\game.exe');

    expect($matchingExecutable)->toBe([1234, 1235]);
});

it('Matches patch regardless of letter casing ', function () {
        $processes = [
            ['pid' => 1234, 'path' => 'C:\\Games\\Example\\game.exe'],
        ];

        $matcher = new ExecutableMatcher;

        $matchingExecutable = $matcher->matchingProcessIds($processes, 'c:\\games\\example\\GAME.EXE');

        expect($matchingExecutable)->toBe([1234]);
});
