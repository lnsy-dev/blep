<?php

use Blep\Parser\BLParser;

function examplePath(string $file = ''): string
{
    return dirname(__DIR__, 3) . '/example/src' . ($file ? '/' . $file : '');
}

beforeEach(function () {
    $this->parser = new BLParser();
});

// ── CateringService.php ───────────────────────────────────────────────────────

it('CateringService: file-global @bl-topic creates Catering Operations', function () {
    $this->parser->addFile(examplePath('CateringService.php'));
    $data = $this->parser->getData();

    expect($data)->toHaveKey('Catering Operations');
});

it('CateringService: @bl-subtopic on inline // comment creates Supplier Portal with two details', function () {
    $this->parser->addFile(examplePath('CateringService.php'));
    $data = $this->parser->getData();

    expect($data['Catering Operations'])->toHaveKey('Supplier Portal');

    $details = array_values(array_filter(
        $data['Catering Operations']['Supplier Portal'],
        fn($item) => $item['type'] === 'detail'
    ));

    expect($details)->toHaveCount(2)
        ->and($details[0]['text'])->toBe('ingredient counts are refreshed only on the first of the month')
        ->and($details[1]['text'])->toBe("there's a special menu reset on Veterans Day");
});

it('CateringService: @bl-subtopic in docblock creates Client Roster', function () {
    $this->parser->addFile(examplePath('CateringService.php'));
    $data = $this->parser->getData();

    expect($data['Catering Operations'])->toHaveKey('Client Roster');

    $details = array_values(array_filter(
        $data['Catering Operations']['Client Roster'],
        fn($item) => $item['type'] === 'detail'
    ));

    // The /* */ single-line comment style is not captured — $inDocblock is set
    // true then immediately false on the same line, so only the // detail is extracted.
    expect($details)->toHaveCount(1)
        ->and($details[0]['text'])->toBe('we pull client preferences from the roster service');
});

// ── MenuRegistry.php ──────────────────────────────────────────────────────────

it('MenuRegistry: file-global @bl-topic creates Menu Registry', function () {
    $this->parser->addFile(examplePath('MenuRegistry.php'));
    $data = $this->parser->getData();

    expect($data)->toHaveKey('Menu Registry');
});

it('MenuRegistry: method-level @bl-topic overrides file-global for getBaseUrl', function () {
    $this->parser->addFile(examplePath('MenuRegistry.php'));
    $data = $this->parser->getData();

    expect($data)->toHaveKey('Catering Operations');
    expect($data['Catering Operations'])->toHaveKey('Supplier Portal');

    $details = array_values(array_filter(
        $data['Catering Operations']['Supplier Portal'],
        fn($item) => $item['type'] === 'detail'
    ));

    expect($details)->toHaveCount(1)
        ->and($details[0]['text'])->toBe('base URL is hard coded');
});

it('MenuRegistry: getDetails and doReset fall back to Menu Registry topic with Web Service subtopic', function () {
    $this->parser->addFile(examplePath('MenuRegistry.php'));
    $data = $this->parser->getData();

    expect($data['Menu Registry'])->toHaveKey('Web Service');

    $webService = $data['Menu Registry']['Web Service'];
    $details    = array_values(array_filter($webService, fn($item) => $item['type'] === 'detail'));
    $seeRefs    = array_values(array_filter($webService, fn($item) => $item['type'] === 'see'));

    // Two methods (getDetails + doReset) each contribute 2 inline details
    expect($details)->toHaveCount(4);

    // Each method docblock has one @bl-see Catering Operations
    expect($seeRefs)->toHaveCount(2)
        ->and($seeRefs[0]['topic'])->toBe('Catering Operations')
        ->and($seeRefs[0]['subtopic'])->toBe('')
        ->and($seeRefs[1]['topic'])->toBe('Catering Operations');
});

// ── Both files together ───────────────────────────────────────────────────────

it('cross-file: Catering Operations Supplier Portal merges entries from both files', function () {
    $this->parser->addFile(examplePath('CateringService.php'));
    $this->parser->addFile(examplePath('MenuRegistry.php'));
    $data = $this->parser->getData();

    $details = array_values(array_filter(
        $data['Catering Operations']['Supplier Portal'],
        fn($item) => $item['type'] === 'detail'
    ));

    // 2 from CateringService (fromSupplierPortal) + 1 from MenuRegistry (getBaseUrl)
    expect($details)->toHaveCount(3);
});

it('addDirectory scans example/src and finds both topics', function () {
    $this->parser->addDirectory(examplePath());
    $data = $this->parser->getData();

    expect($data)->toHaveKey('Catering Operations')
        ->and($data)->toHaveKey('Menu Registry');
});
