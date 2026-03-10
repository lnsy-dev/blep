#!/usr/bin/env php
<?php

require_once __DIR__ . '/../BLParser.php';
require_once __DIR__ . '/../BLHtmlGenerator.php';

class TestRunner {
    private $passed = 0;
    private $failed = 0;
    
    public function assert($condition, $message) {
        if ($condition) {
            $this->passed++;
            echo "✓ PASS: $message\n";
        } else {
            $this->failed++;
            echo "✗ FAIL: $message\n";
        }
    }
    
    public function assertContains($needle, $haystack, $message) {
        $this->assert(strpos($haystack, $needle) !== false, $message);
    }
    
    public function assertCount($expected, $array, $message) {
        $actual = count($array);
        $this->assert($actual === $expected, "$message (expected $expected, got $actual)");
    }
    
    public function summary() {
        $total = $this->passed + $this->failed;
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "Tests: $total, Passed: $this->passed, Failed: $this->failed\n";
        return $this->failed === 0 ? 0 : 1;
    }
}

$test = new TestRunner();
$fixturesDir = __DIR__ . '/fixtures';
$outputDir = __DIR__ . '/output';

// Clean output directory
if (is_dir($outputDir)) {
    array_map('unlink', glob("$outputDir/*"));
    rmdir($outputDir);
}

echo "Running Business Logic Documentation Generator Tests\n";
echo str_repeat('=', 50) . "\n\n";

// Test 1: Parse FilmClub.php
echo "Test Group: FilmClub.php\n";
$parser = new BLParser();
$parser->addFile("$fixturesDir/FilmClub.php");
$data = $parser->getData();

$test->assert(isset($data['Film Club Membership']), "FilmClub: Topic 'Film Club Membership' exists");
$test->assert(isset($data['Film Club Membership']['Eligibility']), "FilmClub: Subtopic 'Eligibility' exists");
$test->assert(isset($data['Film Club Membership']['Subscription Tiers']), "FilmClub: Subtopic 'Subscription Tiers' exists");
$test->assertCount(2, $data['Film Club Membership']['Eligibility'], "FilmClub: Eligibility has 2 details");
$test->assertCount(4, $data['Film Club Membership']['Subscription Tiers'], "FilmClub: Subscription Tiers has 4 details");

// Test 2: Parse Director.php
echo "\nTest Group: Director.php\n";
$parser2 = new BLParser();
$parser2->addFile("$fixturesDir/Director.php");
$data2 = $parser2->getData();

$test->assert(isset($data2['Film Approval']), "Director: Topic 'Film Approval' exists");
$test->assert(isset($data2['Film Approval']['Director Review']), "Director: Subtopic 'Director Review' exists");
$seeRefs = array_filter($data2['Film Approval']['Director Review'], fn($item) => $item['type'] === 'see');
$test->assertCount(1, $seeRefs, "Director: Has 1 see-also reference");
$seeRef = array_values($seeRefs)[0];
$test->assert($seeRef['topic'] === 'Film Club Membership' && $seeRef['subtopic'] === 'Subscription Tiers', "Director: See-also reference is correct");

// Test 3: Parse EdgeCases.php
echo "\nTest Group: EdgeCases.php\n";
$parser3 = new BLParser();
$parser3->addFile("$fixturesDir/EdgeCases.php");
$data3 = $parser3->getData();
$warnings3 = $parser3->getWarnings();

$test->assert(count($warnings3) > 0, "EdgeCases: Generated warnings for orphaned detail");
$test->assert(isset($data3['Edge Case Testing']), "EdgeCases: Topic 'Edge Case Testing' exists");
$test->assert(isset($data3['Second Topic In Same File']), "EdgeCases: Second topic in same file exists");
$test->assert(isset($data3['Edge Case Testing']['Mixed DocBlock']), "EdgeCases: Mixed DocBlock subtopic exists");
$mixedDetails = array_filter($data3['Edge Case Testing']['Mixed DocBlock'], fn($item) => $item['type'] === 'detail');
$test->assertCount(4, $mixedDetails, "EdgeCases: Mixed DocBlock has 4 @bl-detail tags (2 docblock + 2 inline, ignores @param/@return)");

// Test 4: Parse NoTags.php
echo "\nTest Group: NoTags.php\n";
$parser4 = new BLParser();
$parser4->addFile("$fixturesDir/NoTags.php");
$data4 = $parser4->getData();

$test->assertCount(0, $data4, "NoTags: No topics extracted from file without @bl-* tags");

// Test 5: Multi-file topic merging
echo "\nTest Group: Multi-file Topic Merging\n";
$parser5 = new BLParser();
$parser5->addFile("$fixturesDir/MultiFileTopicA.php");
$parser5->addFile("$fixturesDir/MultiFileTopicB.php");
$data5 = $parser5->getData();

$test->assert(isset($data5['Shared Topic']), "MultiFile: Shared Topic exists");
$test->assert(isset($data5['Shared Topic']['From File A']), "MultiFile: Subtopic from file A exists");
$test->assert(isset($data5['Shared Topic']['From File B']), "MultiFile: Subtopic from file B exists");
$test->assertCount(4, $data5['Shared Topic']['From File A'], "MultiFile: File A subtopic has 4 details (3 from A, 1 from B)");

// Test 6: HTML Generation
echo "\nTest Group: HTML Generation\n";
$parserFull = new BLParser();
$parserFull->addDirectory($fixturesDir);
$generator = new BLHtmlGenerator($parserFull->getData(), $outputDir, ['title' => 'Test Documentation']);
$generator->generate();

$test->assert(file_exists("$outputDir/index.html"), "HTML: index.html created");
$test->assert(file_exists("$outputDir/topic-film-club-membership.html"), "HTML: Film Club Membership topic page created");
$test->assert(file_exists("$outputDir/topic-film-approval.html"), "HTML: Film Approval topic page created");

$indexContent = file_get_contents("$outputDir/index.html");
$test->assertContains('Film Club Membership', $indexContent, "HTML: Index contains Film Club Membership");
$test->assertContains('Test Documentation', $indexContent, "HTML: Index contains custom title");

$filmClubContent = file_get_contents("$outputDir/topic-film-club-membership.html");
$test->assertContains('Members must be 18 or older', $filmClubContent, "HTML: Topic page contains detail text");
$test->assertContains('FilmClub.php', $filmClubContent, "HTML: Topic page contains source file reference");

$filmApprovalContent = file_get_contents("$outputDir/topic-film-approval.html");
$test->assertContains('Film Club Membership', $filmApprovalContent, "HTML: Cross-reference link exists");

echo "\n";
exit($test->summary());
