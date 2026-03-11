# Version Control System Support

Blep automatically detects and integrates with your version control system to provide change history and authorship information for business logic documentation.

## Supported Systems

- **Git** — Most common VCS, detected via `.git` directory
- **Subversion (SVN)** — Detected via `.svn` directory
- **Perforce (P4)** — Detected via `p4 info` command

## How It Works

### Automatic Detection

When Blep processes the first file, it automatically detects which VCS is in use:

1. Checks for Git (`.git` directory)
2. Checks for Subversion (`.svn` directory)
3. Checks for Perforce (`p4 info` command)

No configuration needed — detection happens automatically.

### Information Extracted

For each `@bl-detail` tag, Blep extracts:

- **Author** — Who last modified this line
- **Timestamp** — When the line was last modified
- **Change History** — Recent commits affecting this line (up to 10)
- **Commit Messages** — Why changes were made

### Implementation

The VCS support is implemented through a simple interface:

```php
interface VCSInterface
{
    public function getBlame(string $filePath, int $lineNumber): ?array;
    public function getHistory(string $filePath, int $lineNumber, int $limit = 10): array;
    public static function isAvailable(string $filePath): bool;
}
```

Each VCS has its own implementation:
- `GitVCS` — Uses `git blame` and `git log -L`
- `SubversionVCS` — Uses `svn blame --xml` and `svn log --xml`
- `PerforceVCS` — Uses `p4 annotate` and `p4 filelog`

## Adding New VCS Support

To add support for a new VCS:

1. Create a new class implementing `VCSInterface` in `src/VCS/`
2. Implement the three required methods:
   - `getBlame()` — Return author and timestamp for a specific line
   - `getHistory()` — Return change history for a specific line
   - `isAvailable()` — Detect if this VCS is in use
3. Add your class to `VCSFactory::$providers` array

## Fallback Behavior

If no VCS is detected, Blep continues to work but without change history:
- Documentation is still generated
- No author/timestamp information shown
- No change history displayed

This allows Blep to work in any environment, even without version control.
