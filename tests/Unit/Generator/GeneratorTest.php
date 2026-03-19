<?php

use Blep\Parser\BLParser;
use Blep\Generator\HtmlGenerator;
use Blep\Generator\MarkdownGenerator;
use Blep\Generator\SearchIndexGenerator;

beforeEach(function () {
    cleanOutputDir();

    $parser = new BLParser();
    $parser->addDirectory(fixturesPath());
    $this->data = $parser->getData();
    $this->outputDir = outputPath();
});

afterEach(function () {
    cleanOutputDir();
});

// ── HtmlGenerator ────────────────────────────────────────────────────────────

it('HtmlGenerator generates index.html', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir, ['title' => 'Test Documentation']);
    $generator->generate();

    expect(file_exists($this->outputDir . '/index.html'))->toBeTrue();

    $content = file_get_contents($this->outputDir . '/index.html');
    expect($content)->toContain('Film Club Membership')
        ->and($content)->toContain('Test Documentation');
});

it('HtmlGenerator generates topic pages', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/topic-film-club-membership.html'))->toBeTrue()
        ->and(file_exists($this->outputDir . '/topic-film-approval.html'))->toBeTrue();
});

it('HtmlGenerator includes detail text in topic pages', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-film-club-membership.html');
    expect($content)->toContain('Members must be 18 or older')
        ->and($content)->toContain('FilmClub.php');
});

it('HtmlGenerator includes cross-references', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-film-approval.html');
    expect($content)->toContain('Film Club Membership');
});

it('HtmlGenerator generates search.html', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/search.html'))->toBeTrue();
});

it('HtmlGenerator generates changelog.html', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/changelog.html'))->toBeTrue();
});

// ── MarkdownGenerator ────────────────────────────────────────────────────────

it('MarkdownGenerator generates index.md', function () {
    $generator = new MarkdownGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/index.md'))->toBeTrue();
});

it('MarkdownGenerator generates topic markdown files', function () {
    $generator = new MarkdownGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/topic-film-club-membership.md'))->toBeTrue();
});

// ── SearchIndexGenerator ─────────────────────────────────────────────────────

it('SearchIndexGenerator generates search-index.json', function () {
    $generator = new SearchIndexGenerator($this->data, $this->outputDir);
    $generator->generate();

    expect(file_exists($this->outputDir . '/search-index.json'))->toBeTrue();

    $content = file_get_contents($this->outputDir . '/search-index.json');
    $index = json_decode($content, true);

    expect($index)->toBeArray()
        ->and(count($index))->toBeGreaterThan(0);
});
