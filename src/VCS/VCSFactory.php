<?php

namespace Blep\VCS;

class VCSFactory
{
    private static array $providers = [
        GitVCS::class,
        SubversionVCS::class,
        PerforceVCS::class,
    ];

    public static function detect(string $filePath): ?VCSInterface
    {
        foreach (self::$providers as $provider) {
            if ($provider::isAvailable($filePath)) {
                return new $provider();
            }
        }
        return null;
    }
}
