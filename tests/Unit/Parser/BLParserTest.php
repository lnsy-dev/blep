<?php

use Blep\Parser\BLParser;

describe('BLParser', function () {
    beforeEach(function () {
        $this->parser = new BLParser();
    });

    it('parses FilmClub.php correctly', function () {
        $this->parser->addFile(fixturesPath('FilmClub.php'));
        $data = $this->parser->getData();

        expect($data)->toHaveKey('Film Club Membership')
            ->and($data['Film Club Membership'])->toHaveKey('Eligibility')
            ->and($data['Film Club Membership'])->toHaveKey('Subscription Tiers')
            ->and($data['Film Club Membership']['Eligibility'])->toHaveCount(2)
            ->and($data['Film Club Membership']['Subscription Tiers'])->toHaveCount(4);
    });

    it('parses Director.php with see-also references', function () {
        $this->parser->addFile(fixturesPath('Director.php'));
        $data = $this->parser->getData();

        expect($data)->toHaveKey('Film Approval')
            ->and($data['Film Approval'])->toHaveKey('Director Review');

        $seeRefs = array_filter(
            $data['Film Approval']['Director Review'],
            fn($item) => $item['type'] === 'see'
        );

        expect($seeRefs)->toHaveCount(1);

        $seeRef = array_values($seeRefs)[0];
        expect($seeRef['topic'])->toBe('Film Club Membership')
            ->and($seeRef['subtopic'])->toBe('Subscription Tiers');
    });

    it('handles edge cases and generates warnings', function () {
        $this->parser->addFile(fixturesPath('EdgeCases.php'));
        $data = $this->parser->getData();
        $warnings = $this->parser->getWarnings();

        expect($warnings)->not->toBeEmpty()
            ->and($data)->toHaveKey('Edge Case Testing')
            ->and($data)->toHaveKey('Second Topic In Same File')
            ->and($data['Edge Case Testing'])->toHaveKey('Mixed DocBlock');

        $mixedDetails = array_filter(
            $data['Edge Case Testing']['Mixed DocBlock'],
            fn($item) => $item['type'] === 'detail'
        );

        expect($mixedDetails)->toHaveCount(4);
    });

    it('returns empty data for files without @bl-* tags', function () {
        $this->parser->addFile(fixturesPath('NoTags.php'));
        $data = $this->parser->getData();

        expect($data)->toBeEmpty();
    });

    it('merges topics from multiple files', function () {
        $this->parser->addFile(fixturesPath('MultiFileTopicA.php'));
        $this->parser->addFile(fixturesPath('MultiFileTopicB.php'));
        $data = $this->parser->getData();

        expect($data)->toHaveKey('Shared Topic')
            ->and($data['Shared Topic'])->toHaveKey('From File A')
            ->and($data['Shared Topic'])->toHaveKey('From File B')
            ->and($data['Shared Topic']['From File A'])->toHaveCount(4);
    });

    it('can scan directories recursively', function () {
        $this->parser->addDirectory(fixturesPath());
        $data = $this->parser->getData();

        expect($data)->not->toBeEmpty()
            ->and($data)->toHaveKey('Film Club Membership')
            ->and($data)->toHaveKey('Film Approval');
    });

    it('tracks warnings for malformed tags', function () {
        $this->parser->addFile(fixturesPath('EdgeCases.php'));
        $warnings = $this->parser->getWarnings();

        expect($warnings)->toBeArray()
            ->and(count($warnings))->toBeGreaterThan(0);
    });
});
