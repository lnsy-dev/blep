# Migration Guide: v1.x to v2.0

This guide helps you migrate from Blep v1.x to v2.0, which introduces an enterprise-grade PHP architecture.

## What Changed?

Blep v2.0 restructures the codebase to follow modern PHP standards with PSR-4 autoloading, proper namespacing, and professional tooling.

## Quick Migration

### If you used the single-file `bldoc`:

**Before (v1.x):**
```bash
./bldoc src/
```

**After (v2.0):**
```bash
# Download new phar from releases
curl -O https://github.com/yourusername/blep/releases/latest/download/bldoc
chmod +x bldoc
./bldoc src/
```

No changes needed! The CLI interface remains the same.

### If you used Composer:

**Before (v1.x):**
```bash
composer require blep/blep
php vendor/bin/bl-doc-gen.php src/
```

**After (v2.0):**
```bash
composer require blep/blep
./vendor/bin/bldoc src/
```

Just update the command from `bl-doc-gen.php` to `bldoc`.

### If you used from source:

**Before (v1.x):**
```bash
git clone https://github.com/yourusername/blep.git
cd blep
./build.sh
./bldoc src/
```

**After (v2.0):**
```bash
git clone https://github.com/yourusername/blep.git
cd blep
composer install
composer build
./build/bldoc src/
```

## Breaking Changes

### 1. Entry Point Changed

| v1.x | v2.0 |
|------|------|
| `php bl-doc-gen.php` | `./bin/bldoc` |
| `./bldoc` (single-file) | `./build/bldoc` (phar) |

### 2. Build Process Changed

| v1.x | v2.0 |
|------|------|
| `./build.sh` | `composer build` |
| Creates `bldoc` | Creates `build/bldoc` |

### 3. Test Runner Changed

| v1.x | v2.0 |
|------|------|
| `php tests/run-tests.php` | `composer test` or `./vendor/bin/pest` |

### 4. Class Names (for developers extending Blep)

If you were extending or using Blep classes directly:

| v1.x | v2.0 |
|------|------|
| `BLParser` | `Blep\Parser\BLParser` |
| `BLHtmlGenerator` | `Blep\Generator\HtmlGenerator` |
| `BLMarkdownGenerator` | `Blep\Generator\MarkdownGenerator` |
| `BLSearchIndexGenerator` | `Blep\Generator\SearchIndexGenerator` |

**Before (v1.x):**
```php
require_once 'BLParser.php';
$parser = new BLParser();
```

**After (v2.0):**
```php
require 'vendor/autoload.php';
use Blep\Parser\BLParser;
$parser = new BLParser();
```

## What Stayed the Same?

✅ All CLI options and flags  
✅ All `@bl-*` tag syntax  
✅ Generated HTML output format  
✅ PHP 7.4+ compatibility  
✅ Zero runtime dependencies  

## Updating Scripts

### Shell Scripts

**Before:**
```bash
#!/bin/bash
php /path/to/bl-doc-gen.php -o docs/ src/
```

**After:**
```bash
#!/bin/bash
/path/to/bin/bldoc -o docs/ src/
# or if installed via Composer:
./vendor/bin/bldoc -o docs/ src/
```

### CI/CD Pipelines

**Before:**
```yaml
- name: Generate docs
  run: php bl-doc-gen.php -o docs/ src/
```

**After:**
```yaml
- name: Install dependencies
  run: composer install
  
- name: Generate docs
  run: ./vendor/bin/bldoc -o docs/ src/
```

## Benefits of v2.0

- ✅ **PSR-4 Autoloading**: Standard PHP autoloading
- ✅ **Namespaced Classes**: No naming conflicts
- ✅ **Organized Structure**: Clear separation of concerns
- ✅ **Modern Testing**: Pest framework integration
- ✅ **Professional Builds**: Box for phar generation
- ✅ **Better Tooling**: Composer scripts for common tasks
- ✅ **Easier Extension**: Clean architecture for customization

## Need Help?

- Check the [README.md](README.md) for updated documentation
- See [docs/project-structure.md](docs/project-structure.md) for architecture details
- Open an issue on [GitHub](https://github.com/yourusername/blep/issues)

## Rollback

If you need to stay on v1.x:

```bash
composer require blep/blep:^1.0
```

Or download the v1.x release from GitHub releases.
