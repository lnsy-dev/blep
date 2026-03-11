<?php

namespace Blep\VCS;

class PerforceVCS implements VCSInterface
{
    public function getBlame(string $filePath, int $lineNumber): ?array
    {
        $output = shell_exec("p4 annotate -q " . escapeshellarg($filePath) . " 2>/dev/null");
        if (!$output) {
            return null;
        }
        
        $lines = explode("\n", $output);
        if (!isset($lines[$lineNumber - 1])) {
            return null;
        }
        
        $line = $lines[$lineNumber - 1];
        if (preg_match('/^(\d+):\s/', $line, $m)) {
            $change = $m[1];
            $describe = shell_exec("p4 describe -s $change 2>/dev/null");
            if ($describe && preg_match('/Change \d+ by (.+?)@.+? on (.+?)$/m', $describe, $dm)) {
                return [
                    'author' => trim($dm[1]),
                    'timestamp' => strtotime($dm[2])
                ];
            }
        }
        
        return null;
    }

    public function getHistory(string $filePath, int $lineNumber, int $limit = 10): array
    {
        $output = shell_exec("p4 filelog -l -m $limit " . escapeshellarg($filePath) . " 2>/dev/null");
        if (!$output) {
            return [];
        }
        
        $history = [];
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            if (preg_match('/^... #\d+ change (\d+) .+ on (.+?) by (.+?)@/', $line, $m)) {
                $change = $m[1];
                $date = $m[2];
                $author = $m[3];
                
                $describe = shell_exec("p4 describe -s $change 2>/dev/null");
                $message = '';
                if ($describe && preg_match('/\n\n\t(.+?)(?:\n\n|$)/s', $describe, $dm)) {
                    $message = trim(str_replace("\n\t", ' ', $dm[1]));
                }
                
                $history[] = [
                    'hash' => $change,
                    'author' => $author,
                    'timestamp' => strtotime($date),
                    'message' => $message
                ];
                
                if (count($history) >= $limit) {
                    break;
                }
            }
        }
        
        return $history;
    }

    public static function isAvailable(string $filePath): bool
    {
        $output = shell_exec("p4 info 2>/dev/null");
        return $output && strpos($output, 'Client root:') !== false;
    }
}
