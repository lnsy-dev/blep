<?php

namespace Blep\VCS;

interface VCSInterface
{
    public function getBlame(string $filePath, int $lineNumber): ?array;
    public function getHistory(string $filePath, int $lineNumber, int $limit = 10): array;
    public static function isAvailable(string $filePath): bool;
}
