# Command-Line Interface Reference

## Synopsis

```bash
php bl-doc-gen.php [OPTIONS] <source-path> [<source-path> ...]
./bldoc [OPTIONS] <source-path> [<source-path> ...]
```

## Arguments

### Positional Arguments

**source-path** (required, one or more)
- Path to a PHP file or directory to scan
- If directory, scans recursively for `.php` files
- Multiple paths can be specified
- Examples:
  - `src/`
  - `src/ lib/`
  - `app/Models/OrderService.php`

## Options

### Output Options

**-o, --output <directory>**
- Specifies the output directory for generated documentation
- Default: `./bl-docs/`
- Directory will be created if it doesn't exist
- Examples:
  - `-o docs/`
  - `--output /var/www/html/docs/`

**-t, --title <title>**
- Sets the title for the documentation site
- Default: `"Business Logic Documentation"`
- Appears in page titles and headers
- Examples:
  - `-t "My Project Documentation"`
  - `--title "E-Commerce Business Rules"`

### Filtering Options

**--exclude <pattern>**
- Excludes files/directories matching the pattern
- Can be specified multiple times
- Pattern matching is substring-based
- Examples:
  - `--exclude vendor/`
  - `--exclude tests/ --exclude vendor/`
  - `--exclude .git`

### Display Options

**-v, --verbose**
- Enables verbose output
- Shows each file as it's being parsed
- Useful for debugging and monitoring progress
- Example output:
  ```
  Parsing: src/OrderService.php
  Parsing: src/PaymentService.php
  Parsing: src/UserService.php
  ```

**-h, --help**
- Displays help message and exits
- Shows usage, options, and examples
- Exit code: 0

**--version**
- Displays version number and exits
- Format: `bl-doc-gen version X.Y.Z`
- Exit code: 0

## Exit Codes

- **0** - Success
- **1** - Error (invalid arguments, missing files, generation failure)

## Examples

### Basic Usage

Generate docs from a single directory:
```bash
php bl-doc-gen.php src/
```

Generate docs from multiple directories:
```bash
php bl-doc-gen.php src/ lib/ app/
```

Generate docs from a single file:
```bash
php bl-doc-gen.php src/OrderService.php
```

### Custom Output

Specify output directory:
```bash
php bl-doc-gen.php -o documentation/ src/
```

Specify custom title:
```bash
php bl-doc-gen.php -t "My Project Rules" src/
```

Both output and title:
```bash
php bl-doc-gen.php -o docs/ -t "E-Commerce Rules" src/
```

### Filtering

Exclude vendor directory:
```bash
php bl-doc-gen.php --exclude vendor/ .
```

Exclude multiple directories:
```bash
php bl-doc-gen.php --exclude vendor/ --exclude tests/ --exclude node_modules/ .
```

### Verbose Mode

Show files being processed:
```bash
php bl-doc-gen.php -v src/
```

Combine with other options:
```bash
php bl-doc-gen.php -v -o docs/ --exclude vendor/ src/
```

### Single-File Version

All examples work with the single-file version:
```bash
./bldoc src/
./bldoc -o docs/ -t "My Docs" src/
./bldoc -v --exclude vendor/ .
```

## Output

### Standard Output

The tool prints to stdout:
1. Scan summary with statistics
2. Warnings (if any)
3. Generation status
4. Path to generated index.html

Example output:
```
=== Scan Summary ===
Files scanned: 42
Files with BL tags: 8
Topics found: 5
Subtopics found: 12
Details found: 47

=== Warnings ===
⚠ src/Legacy.php:15: @bl-detail without @bl-topic

=== Generating Site ===
✓ Site generated successfully
→ Open: /home/user/project/bl-docs/index.html
```

### Error Output

Errors are printed to stderr:
```
Error: No source paths provided
Error: Source path does not exist: invalid/path
Error: -o requires a value
Error: Unknown option: --invalid
```

## Environment

### Requirements
- PHP 7.4 or higher
- Read access to source files
- Write access to output directory

### No Configuration Files
The tool doesn't use configuration files. All options are specified via command-line arguments.

## Tips

1. **Use absolute paths** for output when generating from different directories
2. **Exclude build artifacts** with `--exclude dist/ --exclude build/`
3. **Use verbose mode** when first scanning a large codebase
4. **Test on small subset** before scanning entire project
5. **Combine multiple source paths** instead of running multiple times

## Common Patterns

### CI/CD Integration
```bash
#!/bin/bash
php bl-doc-gen.php -o public/docs/ -t "API Documentation" src/
```

### Development Workflow
```bash
# Quick check during development
php bl-doc-gen.php -v src/ | grep "Topics found"
```

### Documentation Server
```bash
php bl-doc-gen.php -o /var/www/docs/ src/
```

### Pre-commit Hook
```bash
#!/bin/bash
php bl-doc-gen.php src/ > /dev/null
if [ $? -ne 0 ]; then
    echo "Documentation generation failed"
    exit 1
fi
```
