<?php

namespace Blep\Command;

use Blep\Parser\BLParser;
use Blep\Generator\HtmlGenerator;
use Blep\Generator\MarkdownGenerator;
use Blep\Generator\SearchIndexGenerator;

class GenerateCommand
{
    private const VERSION = '1.0.0';

    public function run(array $argv): int
    {
        $options = $this->parseArgs($argv);

        if (empty($options['sources'])) {
            fwrite(STDERR, "Error: No source paths provided\n\n");
            $this->printUsage();
            return 1;
        }

        foreach ($options['sources'] as $source) {
            if (!file_exists($source)) {
                fwrite(STDERR, "Error: Source path does not exist: $source\n");
                return 1;
            }
        }

        $parser = new BLParser();
        $totalFiles = 0;

        foreach ($options['sources'] as $source) {
            $totalFiles += $this->scanPath($source, $parser, $options['exclude'], $options['verbose']);
        }

        $data = $parser->getData();
        $warnings = $parser->getWarnings();
        $stats = $this->countStats($data);

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

        echo "\n=== Generating Site ===\n";

        if (empty($data)) {
            echo "No business logic documentation found.\n";
            echo "Generating empty site...\n";
        }

        try {
            $generator = new HtmlGenerator($data, $options['output'], ['title' => $options['title']]);
            $generator->generate();
            
            $mdGenerator = new MarkdownGenerator($data, $options['output'], ['title' => $options['title']]);
            $mdGenerator->generate();
            
            $searchGenerator = new SearchIndexGenerator($data, $options['output']);
            $searchGenerator->generate();
            
            $indexPath = realpath($options['output'] . '/index.html');
            echo "✓ Site generated successfully\n";
            echo "→ Open: $indexPath\n";
            return 0;
        } catch (\Exception $e) {
            fwrite(STDERR, "Error generating site: " . $e->getMessage() . "\n");
            return 1;
        }
    }

    private function parseArgs(array $argv): array
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
                $this->printUsage();
                exit(0);
            }

            if ($arg === '--version') {
                echo "bl-doc-gen version " . self::VERSION . "\n";
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

    private function printUsage(): void
    {
        printf(<<<USAGE
Business Logic Documentation Generator v%s

Usage: bldoc [options] <source-path> [<source-path> ...]

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
  bldoc src/
  bldoc -o docs/ -t "My Project" src/ lib/
  bldoc --exclude vendor/ --exclude tests/ .

USAGE
, self::VERSION);
    }

    private function shouldExclude(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $pattern = rtrim($pattern, '/');
            if (strpos($path, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    private function scanPath(string $path, BLParser $parser, array $exclude, bool $verbose): int
    {
        $count = 0;

        if (is_file($path)) {
            if ($this->shouldExclude($path, $exclude)) {
                return 0;
            }
            if ($verbose) {
                echo "Parsing: $path\n";
            }
            $parser->addFile($path);
            return 1;
        }

        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $filePath = $file->getPathname();
                    if ($this->shouldExclude($filePath, $exclude)) {
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

    private function countStats(array $data): array
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
}
