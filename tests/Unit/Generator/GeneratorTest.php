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

// ── Bug: multi-file subtopic shows all source file:line references ────────────

it('HtmlGenerator shows source file:line for every detail, not just the first', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicA.php'));
    $parser->addFile(fixturesPath('MultiFileTopicB.php'));
    $data = $parser->getData();

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-shared-topic.html');

    // "From File A" subtopic has details from both files — both must appear
    expect($content)->toContain('MultiFileTopicA.php')
        ->and($content)->toContain('MultiFileTopicB.php');
});

// ── Bug: ">code" link shows the correct function for each detail ──────────────

it('HtmlGenerator code block for method-level detail shows that method, not a different one', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicB.php'));
    $data = $parser->getData();

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-shared-topic.html');

    // The detail tagged inside methodB's docblock must produce a code block
    // containing "methodB", not some other unrelated function.
    expect($content)->toContain('methodB');
});

it('extractSnippet for file-level docblock does not include the full class body', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicA.php'));
    $data = $parser->getData();

    // First detail in "From File A" comes from the file-level docblock —
    // its snippet should be the class declaration, NOT include methodA's body.
    $details = array_values(array_filter(
        $data['Shared Topic']['From File A'],
        fn($item) => $item['type'] === 'detail'
    ));

    expect($details)->not->toBeEmpty();
    $snippet = $details[0]['snippet'];

    // Should contain the class declaration line
    expect($snippet)->toContain('class MultiFileTopicA');
    // Should NOT contain methodA's inline detail (which is deeper in the class body)
    expect($snippet)->not->toContain('Inline detail from file A');
});

// ── Three-file scenario (exact bug report replication) ────────────────────────

it('HtmlGenerator shows all three source files when subtopic spans three files', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicA.php'));
    $parser->addFile(fixturesPath('MultiFileTopicB.php'));
    $parser->addFile(fixturesPath('MultiFileTopicC.php'));
    $data = $parser->getData();

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-shared-topic.html');

    expect($content)->toContain('MultiFileTopicA.php')
        ->and($content)->toContain('MultiFileTopicB.php')
        ->and($content)->toContain('MultiFileTopicC.php');
});

it('three-file subtopic contains all detail text from every contributing file', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicA.php'));
    $parser->addFile(fixturesPath('MultiFileTopicB.php'));
    $parser->addFile(fixturesPath('MultiFileTopicC.php'));
    $data = $parser->getData();

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-shared-topic.html');

    expect($content)->toContain('This detail comes from MultiFileTopicA.php')
        ->and($content)->toContain('This adds to the same subtopic from file A')
        ->and($content)->toContain('Third file also contributes to From File A');
});

// ── Per-method snippets show the right function (MultiMethod fixture) ─────────

it('each method-level detail gets a code block containing its own function name', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiMethod.php'));
    $data = $parser->getData();

    $validationDetails = array_values(array_filter(
        $data['Payment Processing']['Validation'],
        fn($item) => $item['type'] === 'detail'
    ));

    expect($validationDetails)->toHaveCount(2);

    // First detail is tagged inside validateCard — snippet must contain validateCard
    expect($validationDetails[0]['snippet'])->toContain('validateCard');
    // Second detail is tagged inside validateCvv — snippet must contain validateCvv
    expect($validationDetails[1]['snippet'])->toContain('validateCvv');
    // Neither snippet should bleed into the other method
    expect($validationDetails[0]['snippet'])->not->toContain('validateCvv');
    expect($validationDetails[1]['snippet'])->not->toContain('validateCard');
});

it('HtmlGenerator renders separate code blocks for each detail in a multi-method class', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiMethod.php'));
    $data = $parser->getData();

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-payment-processing.html');

    // Both method names must appear in their respective code blocks
    expect($content)->toContain('validateCard')
        ->and($content)->toContain('validateCvv')
        ->and($content)->toContain('processRefund')
        // All detail text must appear
        ->and($content)->toContain('Credit card numbers must pass Luhn check')
        ->and($content)->toContain('CVV must be exactly 3 digits')
        ->and($content)->toContain('Refunds are only allowed within 30 days')
        // Source file references must appear for each detail
        ->and($content)->toContain('MultiMethod.php');
});

it('HtmlGenerator includes cross-references', function () {
    $generator = new HtmlGenerator($this->data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-film-approval.html');
    expect($content)->toContain('Film Club Membership');
});

// ── Bug: @bl-see without subtopic was always shown as unresolved/crossed-out ──

it('HtmlGenerator resolves @bl-see references that name only a topic with no subtopic', function () {
    $parser = new BLParser();
    $parser->addFile(fixturesPath('MultiFileTopicA.php')); // provides 'Shared Topic'
    // Inline fixture: a see-ref pointing at the topic with no subtopic
    $tmpFile = sys_get_temp_dir() . '/blep_see_test_' . getmypid() . '.php';
    file_put_contents($tmpFile, <<<'PHP'
<?php
/**
 * @bl-topic Other Topic
 * @bl-subtopic Links
 * @bl-see Shared Topic
 */
class SeeTest {}
PHP);
    $parser->addFile($tmpFile);
    $data = $parser->getData();
    unlink($tmpFile);

    $generator = new HtmlGenerator($data, $this->outputDir);
    $generator->generate();

    $content = file_get_contents($this->outputDir . '/topic-other-topic.html');

    // The cross-reference should resolve and NOT be marked unresolved
    expect($content)->not->toContain('class="unresolved"')
        // Link should point to the topic page directly (no empty anchor)
        ->and($content)->toContain('href="topic-shared-topic.html"')
        // Display text should be just the topic name, no trailing arrow
        ->and($content)->toContain('>Shared Topic<');
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
