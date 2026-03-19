<?php

namespace Blep\Parser;

use Blep\VCS\VCSFactory;
use Blep\VCS\VCSInterface;

class BLParser
{
    private array $data = [];
    private array $warnings = [];
    private ?VCSInterface $vcs = null;

    public function addFile(string $filePath): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->warnings[] = "Cannot read file: $filePath";
            return;
        }

        // Detect VCS on first file
        if ($this->vcs === null) {
            $this->vcs = VCSFactory::detect($filePath);
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $fileTopic = null;
        $fileSubtopic = null;
        $currentTopic = null;
        $currentSubtopic = null;
        $currentRationale = null;
        $inDocblock = false;
        $seenClassOrFunction = false;
        $braceDepth = 0;

        foreach ($lines as $lineNum => $line) {
            $lineNumber = $lineNum + 1;

            // Track block comment state (/** and /*)
            if (preg_match('#/\*#', $line)) {
                $inDocblock = true;
                // Only reset to file defaults when entering a class/method-level docblock
                // (brace depth <= 1 means we're at file or class scope, not inside a method body)
                if ($seenClassOrFunction && $braceDepth <= 1) {
                    $currentTopic = $fileTopic;
                    $currentSubtopic = $fileSubtopic;
                    $currentRationale = null;
                }
            }
            if (preg_match('#\*/#', $line)) {
                $inDocblock = false;
            }

            // Track brace depth to distinguish method docblocks from inline block comments
            if (!$inDocblock) {
                $braceDepth += substr_count($line, '{') - substr_count($line, '}');
            }

            // Track when we reach the first class/function declaration (end of file-level scope)
            if (!$inDocblock && !$seenClassOrFunction && preg_match('/^\s*(class\s|function\s)/', $line)) {
                $seenClassOrFunction = true;
            }

            // Extract @bl-* tags
            if ($inDocblock && preg_match('#@bl-(topic|subtopic|detail|details|see|rationale)\s+(.+)#', $line, $m)) {
                $tag = $m[1];
                $text = trim($m[2]);
            } elseif (preg_match('#//\s*@bl-(topic|subtopic|detail|details|see|rationale)\s+(.+)#', $line, $m)) {
                $tag = $m[1];
                $text = trim($m[2]);
            } else {
                continue;
            }

            // Normalize @bl-details to @bl-detail
            if ($tag === 'details') {
                $tag = 'detail';
            }

            // Process tag
            if ($tag === 'topic') {
                $currentTopic = $text;
                $currentSubtopic = null;
                $currentRationale = null;
                // File-level docblock sets the inherited defaults for all functions
                if (!$seenClassOrFunction) {
                    $fileTopic = $text;
                    $fileSubtopic = null;
                }
                if (!isset($this->data[$currentTopic])) {
                    $this->data[$currentTopic] = [];
                }
            } elseif ($tag === 'subtopic') {
                if ($currentTopic === null) {
                    $this->warnings[] = "$filePath:$lineNumber: @bl-subtopic without @bl-topic";
                    continue;
                }
                $currentSubtopic = $text;
                $currentRationale = null;
                // File-level subtopic also sets the inherited default
                if (!$seenClassOrFunction) {
                    $fileSubtopic = $text;
                }
                if (!isset($this->data[$currentTopic][$currentSubtopic])) {
                    $this->data[$currentTopic][$currentSubtopic] = [];
                }
            } elseif ($tag === 'rationale') {
                $currentRationale = $text;
            } elseif ($tag === 'detail') {
                if ($currentTopic === null) {
                    $this->warnings[] = "$filePath:$lineNumber: @bl-detail without @bl-topic";
                    continue;
                }
                $subtopic = $currentSubtopic ?? 'General';
                if (!isset($this->data[$currentTopic][$subtopic])) {
                    $this->data[$currentTopic][$subtopic] = [];
                }
                $this->data[$currentTopic][$subtopic][] = [
                    'type' => 'detail',
                    'text' => $text,
                    'file' => basename($filePath),
                    'fullPath' => $filePath,
                    'line' => $lineNumber,
                    'snippet' => $this->extractSnippet($lines, $lineNum),
                    'blame' => $this->getBlame($filePath, $lineNumber),
                    'history' => $this->getHistory($filePath, $lineNumber),
                    'rationale' => $currentRationale
                ];
                $currentRationale = null;
            } elseif ($tag === 'see') {
                if ($currentTopic === null) {
                    $this->warnings[] = "$filePath:$lineNumber: @bl-see without @bl-topic";
                    continue;
                }
                $subtopic = $currentSubtopic ?? 'General';
                if (!isset($this->data[$currentTopic][$subtopic])) {
                    $this->data[$currentTopic][$subtopic] = [];
                }
                $parts = explode(':', $text, 2);
                $this->data[$currentTopic][$subtopic][] = [
                    'type' => 'see',
                    'topic' => trim($parts[0]),
                    'subtopic' => isset($parts[1]) ? trim($parts[1]) : '',
                    'file' => basename($filePath),
                    'line' => $lineNumber
                ];
            }
        }
    }

    public function addDirectory(string $dirPath, bool $recursive = true): void
    {
        if (!is_dir($dirPath)) {
            $this->warnings[] = "Not a directory: $dirPath";
            return;
        }

        $iterator = $recursive 
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath))
            : new \DirectoryIterator($dirPath);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->addFile($file->getPathname());
            }
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    private function getBlame(string $filePath, int $lineNumber): ?array
    {
        return $this->vcs ? $this->vcs->getBlame($filePath, $lineNumber) : null;
    }

    private function getHistory(string $filePath, int $lineNumber, int $limit = 10): array
    {
        return $this->vcs ? $this->vcs->getHistory($filePath, $lineNumber, $limit) : [];
    }

    private function getGitBlame(string $filePath, int $lineNumber): ?array
    {
        $output = shell_exec("git blame -L $lineNumber,$lineNumber --porcelain " . escapeshellarg($filePath) . " 2>/dev/null");
        if (!$output) {
            return null;
        }
        
        $lines = explode("\n", $output);
        $author = null;
        $timestamp = null;
        
        foreach ($lines as $line) {
            if (preg_match('/^author (.+)$/', $line, $m)) {
                $author = $m[1];
            } elseif (preg_match('/^author-time (\d+)$/', $line, $m)) {
                $timestamp = (int)$m[1];
            }
        }
        
        return $author && $timestamp ? ['author' => $author, 'timestamp' => $timestamp] : null;
    }

    private function getGitHistory(string $filePath, int $lineNumber, int $limit = 10): array
    {
        $output = shell_exec("git log -L $lineNumber,$lineNumber:" . escapeshellarg($filePath) . " --pretty=format:'%H|%an|%at|%s' -n $limit 2>/dev/null");
        if (!$output) {
            return [];
        }
        
        $history = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            if (strpos($line, '|') === false) {
                continue;
            }
            $parts = explode('|', $line, 4);
            if (count($parts) === 4) {
                $history[] = [
                    'hash' => $parts[0],
                    'author' => $parts[1],
                    'timestamp' => (int)$parts[2],
                    'message' => $parts[3]
                ];
            }
        }
        
        return array_slice($history, 0, $limit);
    }

    private function extractSnippet(array $lines, int $centerLine, int $contextLines = 3): string
    {
        // Find the function after the docblock
        $start = $centerLine;
        
        // Skip forward past the end of docblock if we're in one
        for ($i = $centerLine; $i < count($lines); $i++) {
            if (preg_match('#\*/#', $lines[$i])) {
                $start = $i + 1;
                break;
            }
        }
        
        // Find the actual function/class declaration
        $foundFunction = false;
        for ($i = $start; $i < min($start + 10, count($lines)); $i++) {
            if (preg_match('/^\s*(public|private|protected|function|class)\s/', $lines[$i])) {
                $start = $i;
                $foundFunction = true;
                break;
            }
        }
        
        if (!$foundFunction) {
            return implode("\n", array_slice($lines, max(0, $centerLine - $contextLines), $contextLines * 2 + 1));
        }
        
        // Find the end of the function (matching braces)
        $braceCount = 0;
        $inFunction = false;
        $end = $start;
        
        for ($i = $start; $i < count($lines); $i++) {
            $line = $lines[$i];
            $braceCount += substr_count($line, '{') - substr_count($line, '}');
            
            if (strpos($line, '{') !== false) {
                $inFunction = true;
            }
            
            if ($inFunction && $braceCount === 0) {
                $end = $i;
                break;
            }
        }
        
        if ($end <= $start) {
            $end = min(count($lines) - 1, $start + 20);
        }
        
        return implode("\n", array_slice($lines, $start, $end - $start + 1));
    }
}
