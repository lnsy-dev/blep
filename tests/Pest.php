<?php

use Blep\Parser\BLParser;
use Blep\Generator\HtmlGenerator;
use Blep\Generator\MarkdownGenerator;
use Blep\Generator\SearchIndexGenerator;

uses()->in(__DIR__);

function fixturesPath(string $file = ''): string
{
    return __DIR__ . '/fixtures' . ($file ? '/' . $file : '');
}

function outputPath(string $file = ''): string
{
    return __DIR__ . '/output' . ($file ? '/' . $file : '');
}

function cleanOutputDir(): void
{
    $outputDir = outputPath();
    if (is_dir($outputDir)) {
        array_map('unlink', glob("$outputDir/*"));
        rmdir($outputDir);
    }
}

expect()->extend('toHaveKey', function (string $key) {
    expect($this->value)->toBeArray()
        ->and(isset($this->value[$key]))->toBeTrue();
    
    return $this;
});
