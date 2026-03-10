# Blep - Business Logic Documentation Generator

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub Pages](https://img.shields.io/badge/docs-GitHub%20Pages-blue)](https://yourusername.github.io/blep)

A lightweight PHP tool that extracts business logic documentation from code comments and generates a clean, browsable static HTML site.

## Purpose

Document your application's business rules, workflows, and domain logic directly in your code using simple `@bl-*` tags. This tool scans your PHP files and generates a structured documentation site that helps developers understand the "why" behind the code.

## Features

- **Simple annotation syntax** — Use `@bl-topic`, `@bl-subtopic`, `@bl-detail`, and `@bl-see` tags in comments
- **Zero dependencies** — Pure PHP 7.4+, no external libraries required
- **Single-file distribution** — Download one file and run it
- **Fast scanning** — Recursively processes directories
- **Clean HTML output** — Responsive, readable documentation site
- **Cross-references** — Link related topics with `@bl-see`

## Installation

### Option 1: Composer (Recommended)

```bash
composer global require blep/blep
```

### Option 2: Single-file download

```bash
curl -O https://github.com/yourusername/blep/releases/latest/download/bldoc
chmod +x bldoc
```

### Option 3: From source

```bash
git clone https://github.com/yourusername/blep.git
cd blep
./build.sh  # Creates single-file 'bldoc'
```

## Quick Start

1. **Add tags to your PHP code:**
   ```php
   /**
    * @bl-topic Order Processing
    * @bl-detail Orders over $1000 require manager approval
    */
   ```

2. **Generate documentation:**
   ```bash
   ./bldoc src/
   ```

3. **Browse your docs:**
   ```bash
   open bl-docs/index.html
   ```

## Usage

### Basic

```bash
php bl-doc-gen.php src/
```

### With options

```bash
# Custom output directory and title
php bl-doc-gen.php -o docs/ -t "My Project Docs" src/

# Exclude directories
php bl-doc-gen.php --exclude vendor/ --exclude tests/ .

# Verbose output
php bl-doc-gen.php -v src/ lib/

# Using single-file version
./bldoc src/
```

### Command-line options

| Option | Description | Default |
|--------|-------------|---------|
| `-o, --output <dir>` | Output directory | `./bl-docs/` |
| `-t, --title <title>` | Site title | `"Business Logic Documentation"` |
| `--exclude <pattern>` | Exclude pattern (repeatable) | None |
| `-v, --verbose` | Show each file as it's parsed | Off |
| `-h, --help` | Show help message | — |
| `--version` | Show version | — |

## Documentation

- [Quick Start Guide](docs/quickstart.md)
- [CLI Reference](docs/cli-reference.md)
- [Project Structure](docs/project-structure.md)

## Tag Reference

### `@bl-topic`

Defines a top-level documentation topic.

```php
/**
 * @bl-topic Order Processing
 */
class OrderService {
    // ...
}
```

### `@bl-subtopic`

Creates a subsection within the current topic.

```php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 */
```

### `@bl-detail` / `@bl-details`

Documents a specific business rule or workflow step.

```php
/**
 * @bl-detail Orders over $1000 require manager approval
 */
public function createOrder($data) {
    // @bl-detail Inventory is reserved for 15 minutes during checkout
    $this->reserveInventory($data['items']);
}
```

### `@bl-see`

Creates cross-references to related topics.

```php
/**
 * @bl-see Payment Processing
 * @bl-see Inventory Management: Stock Reservation
 */
```

## Example Output

The tool generates:

- `bl-docs/index.html` — Lists all topics
- `bl-docs/topic-order-processing.html` — Individual topic pages

Each detail includes the source file and line number for easy reference.

## Requirements

- PHP 7.4 or higher
- No PHP extensions required beyond standard installation

## Development

### Building

```bash
./build.sh
```

Creates `bldoc` — a single-file version with all classes bundled.

### Testing

```bash
# Test on example project
php bl-doc-gen.php -v example/src/

# Run test suite
php tests/run-tests.php
```

## Contributing

Contributions welcome! Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for guidelines.

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Links

- [Documentation](https://yourusername.github.io/blep)
- [GitHub Repository](https://github.com/yourusername/blep)
- [Issue Tracker](https://github.com/yourusername/blep/issues)
- [Releases](https://github.com/yourusername/blep/releases)