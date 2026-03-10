#!/usr/bin/env php
<?php

const VERSION = '1.0.0';

require_once __DIR__ . '/BLParser.php';
require_once __DIR__ . '/BLHtmlGenerator.php';
require_once __DIR__ . '/BLMarkdownGenerator.php';

function printUsage(): void
{
    printf(<<<USAGE
Business Logic Documentation Generator v%s

Usage: php bl-doc-gen.php [options] <source-path> [<source-path> ...]

Arguments:
  source-path              One or more PHP files or directories to scan

Options:
  -o, --output <dir>       Output directory (default: ./bl-docs/)
  -t, --title <title>      Site title (default: "Business Logic Documentation")
  --exclude <pattern>      Exclude pattern (can be used multiple times)
  -v, --verbose            Verbose output
  -h, --help               Show this help
  --version                Show version

Examples:
  php bl-doc-gen.php src/
  php bl-doc-gen.php -o docs/ -t "My Project" src/ lib/
  php bl-doc-gen.php --exclude vendor/ --exclude tests/ .

USAGE
, VERSION);
}

function parseArgs(array $argv): array
{
    $options = [
        'sources' => [],
        'output' => './bl-docs/',
        'title' => 'Business Logic Documentation',
        'exclude' => [],
        'verbose' => false
    ];

    for ($i = 1; $i < count($argv); $i++) {
        $arg = $argv[$i];

        if ($arg === '-h' || $arg === '--help') {
            printUsage();
            exit(0);
        }

        if ($arg === '--version') {
            echo "bl-doc-gen version " . VERSION . "\n";
            exit(0);
        }

        if ($arg === '-v' || $arg === '--verbose') {
            $options['verbose'] = true;
            continue;
        }

        if ($arg === '-o' || $arg === '--output') {
            if (!isset($argv[$i + 1])) {
                fwrite(STDERR, "Error: {$arg} requires a value\n");
                exit(1);
            }
            $options['output'] = $argv[++$i];
            continue;
        }

        if ($arg === '-t' || $arg === '--title') {
            if (!isset($argv[$i + 1])) {
                fwrite(STDERR, "Error: {$arg} requires a value\n");
                exit(1);
            }
            $options['title'] = $argv[++$i];
            continue;
        }

        if ($arg === '--exclude') {
            if (!isset($argv[$i + 1])) {
                fwrite(STDERR, "Error: --exclude requires a value\n");
                exit(1);
            }
            $options['exclude'][] = $argv[++$i];
            continue;
        }

        if ($arg[0] === '-') {
            fwrite(STDERR, "Error: Unknown option: {$arg}\n");
            exit(1);
        }

        $options['sources'][] = $arg;
    }

    return $options;
}

function shouldExclude(string $path, array $patterns): bool
{
    foreach ($patterns as $pattern) {
        $pattern = rtrim($pattern, '/');
        if (strpos($path, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

function scanPath(string $path, BLParser $parser, array $exclude, bool $verbose): int
{
    $count = 0;

    if (is_file($path)) {
        if (shouldExclude($path, $exclude)) {
            return 0;
        }
        if ($verbose) {
            echo "Parsing: $path\n";
        }
        $parser->addFile($path);
        return 1;
    }

    if (is_dir($path)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (shouldExclude($filePath, $exclude)) {
                    continue;
                }
                if ($verbose) {
                    echo "Parsing: $filePath\n";
                }
                $parser->addFile($filePath);
                $count++;
            }
        }
    }

    return $count;
}

function countStats(array $data): array
{
    $stats = [
        'topics' => count($data),
        'subtopics' => 0,
        'details' => 0,
        'files_with_tags' => 0
    ];

    $files = [];
    foreach ($data as $topic => $subtopics) {
        foreach ($subtopics as $subtopic => $items) {
            $stats['subtopics']++;
            foreach ($items as $item) {
                if ($item['type'] === 'detail') {
                    $stats['details']++;
                }
                $files[$item['file']] = true;
            }
        }
    }

    $stats['files_with_tags'] = count($files);
    return $stats;
}

// Main execution
$options = parseArgs($argv);

if (empty($options['sources'])) {
    fwrite(STDERR, "Error: No source paths provided\n\n");
    printUsage();
    exit(1);
}

// Validate source paths
foreach ($options['sources'] as $source) {
    if (!file_exists($source)) {
        fwrite(STDERR, "Error: Source path does not exist: $source\n");
        exit(1);
    }
}

// Parse files
$parser = new BLParser();
$totalFiles = 0;

foreach ($options['sources'] as $source) {
    $totalFiles += scanPath($source, $parser, $options['exclude'], $options['verbose']);
}

$data = $parser->getData();
$warnings = $parser->getWarnings();
$stats = countStats($data);

// Print summary
echo "\n";
echo "=== Scan Summary ===\n";
echo "Files scanned: $totalFiles\n";
echo "Files with BL tags: {$stats['files_with_tags']}\n";
echo "Topics found: {$stats['topics']}\n";
echo "Subtopics found: {$stats['subtopics']}\n";
echo "Details found: {$stats['details']}\n";

if (!empty($warnings)) {
    echo "\n=== Warnings ===\n";
    foreach ($warnings as $warning) {
        echo "⚠ $warning\n";
    }
}

// Generate site
echo "\n=== Generating Site ===\n";

if (empty($data)) {
    echo "No business logic documentation found.\n";
    echo "Generating empty site...\n";
}

try {
    $generator = new BLHtmlGenerator($data, $options['output'], ['title' => $options['title']]);
    $generator->generate();
    
    $mdGenerator = new BLMarkdownGenerator($data, $options['output'], ['title' => $options['title']]);
    $mdGenerator->generate();
    
    $indexPath = realpath($options['output'] . '/index.html');
    echo "✓ Site generated successfully\n";
    echo "→ Open: $indexPath\n";
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "Error generating site: " . $e->getMessage() . "\n");
    exit(1);
}
