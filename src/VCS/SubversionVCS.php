<?php

namespace Blep\VCS;

class SubversionVCS implements VCSInterface
{
    public function getBlame(string $filePath, int $lineNumber): ?array
    {
        $output = shell_exec("svn blame --xml " . escapeshellarg($filePath) . " 2>/dev/null");
        if (!$output) {
            return null;
        }
        
        $xml = @simplexml_load_string($output);
        if (!$xml) {
            return null;
        }
        
        $entries = $xml->xpath("//entry[@line-number='$lineNumber']");
        if (empty($entries)) {
            return null;
        }
        
        $entry = $entries[0];
        $author = (string)$entry->commit->author;
        $date = (string)$entry->commit->date;
        
        return $author && $date ? [
            'author' => $author,
            'timestamp' => strtotime($date)
        ] : null;
    }

    public function getHistory(string $filePath, int $lineNumber, int $limit = 10): array
    {
        $output = shell_exec("svn log --xml -l $limit " . escapeshellarg($filePath) . " 2>/dev/null");
        if (!$output) {
            return [];
        }
        
        $xml = @simplexml_load_string($output);
        if (!$xml) {
            return [];
        }
        
        $history = [];
        foreach ($xml->logentry as $entry) {
            $history[] = [
                'hash' => 'r' . (string)$entry['revision'],
                'author' => (string)$entry->author,
                'timestamp' => strtotime((string)$entry->date),
                'message' => trim((string)$entry->msg)
            ];
        }
        
        return $history;
    }

    public static function isAvailable(string $filePath): bool
    {
        $dir = dirname($filePath);
        while ($dir !== '/') {
            if (is_dir($dir . '/.svn')) {
                return true;
            }
            $dir = dirname($dir);
        }
        return false;
    }
}
