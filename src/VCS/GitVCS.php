<?php

namespace Blep\VCS;

class GitVCS implements VCSInterface
{
    public function getBlame(string $filePath, int $lineNumber): ?array
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

    public function getHistory(string $filePath, int $lineNumber, int $limit = 10): array
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

    public static function isAvailable(string $filePath): bool
    {
        $dir = dirname($filePath);
        while ($dir !== '/' && dirname($dir) !== $dir) {
            if (is_dir($dir . '/.git')) {
                return true;
            }
            $dir = dirname($dir);
        }
        return is_dir($dir . '/.git');
    }
}
