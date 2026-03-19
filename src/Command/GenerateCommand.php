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
        if (isset($argv[1]) && $argv[1] === 'generate-template-folder') {
            return $this->runGenerateTemplateFolder($argv);
        }

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

        if ($options['templateFolder'] !== null && !is_dir($options['templateFolder'])) {
            fwrite(STDERR, "Error: Template folder does not exist: {$options['templateFolder']}\n");
            return 1;
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
            $generator = new HtmlGenerator($data, $options['output'], [
                'title'          => $options['title'],
                'templateFolder' => $options['templateFolder'],
            ]);
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
            'sources'        => [],
            'output'         => './bl-docs/',
            'title'          => 'Business Logic Documentation',
            'exclude'        => [],
            'verbose'        => false,
            'templateFolder' => null,
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

            if ($arg === '--template-folder') {
                if (!isset($argv[$i + 1])) {
                    fwrite(STDERR, "Error: --template-folder requires a value\n");
                    exit(1);
                }
                $options['templateFolder'] = $argv[++$i];
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

Usage: blep [options] <source-path> [<source-path> ...]
       blep generate-template-folder [path]

Arguments:
  source-path              One or more PHP files or directories to scan

Options:
  -o, --output <dir>       Output directory (default: ./bl-docs/)
  -t, --title <title>      Site title (default: "Business Logic Documentation")
  --template-folder <dir>  Use custom templates from this directory
  --exclude <pattern>      Exclude pattern (can be used multiple times)
  -v, --verbose            Verbose output
  -h, --help               Show this help
  --version                Show version

Subcommands:
  generate-template-folder [path]
                           Create a template folder with default templates
                           for editing. Default path: ./blep-templates/

Examples:
  blep src/
  blep -o docs/ -t "My Project" src/ lib/
  blep --exclude vendor/ --exclude tests/ .
  blep generate-template-folder
  blep generate-template-folder ./my-theme/
  blep --template-folder ./my-theme/ src/

USAGE
, self::VERSION);
    }

    private function runGenerateTemplateFolder(array $argv): int
    {
        $targetDir = rtrim($argv[2] ?? './blep-templates', '/');

        if (is_dir($targetDir)) {
            fwrite(STDERR, "Error: Directory already exists: $targetDir\n");
            fwrite(STDERR, "Delete it or specify a different path.\n");
            return 1;
        }

        mkdir($targetDir, 0755, true);

        $builtinDir = __DIR__ . '/../Templates';
        $templates = ['index.php', 'topic.php', 'changelog.php', 'search.php', 'styles.css'];

        foreach ($templates as $tpl) {
            file_put_contents("$targetDir/$tpl", file_get_contents("$builtinDir/$tpl"));
        }

        echo "Template folder created: $targetDir\n";
        echo "Files created:\n";
        foreach ($templates as $tpl) {
            echo "  $targetDir/$tpl\n";
        }
        echo "\nEdit these files to customize your documentation theme.\n";
        echo "Then run: blep --template-folder $targetDir <source-path>\n";

        return 0;
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
