# Blep - Business Logic Documentation Generator

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub Pages](https://img.shields.io/badge/docs-GitHub%20Pages-blue)](https://yourusername.github.io/blep)

A lightweight PHP tool that extracts business logic documentation from code comments and generates a clean, browsable static HTML site.

## Purpose

Document your application's business rules, workflows, and domain logic directly in your code using simple `@bl-*` tags. This tool scans your PHP files and generates a structured documentation site that helps developers understand the "why" behind the code.

## Features

- **Simple annotation syntax** — Use `@bl-topic`, `@bl-subtopic`, `@bl-detail`, `@bl-rationale`, and `@bl-see` tags in comments
- **Zero dependencies** — Pure PHP 7.4+, no external libraries required
- **Single-file distribution** — Download one file and run it
- **Fast scanning** — Recursively processes directories
- **Clean HTML output** — Responsive, readable documentation site
- **Cross-references** — Link related topics with `@bl-see`
- **Change history** — Track business rule changes over time with git integration
- **Interactive search** — Full-text search powered by Fuse.js
- **Rationale tracking** — Document why rules exist with `@bl-rationale`

## Installation

### Option 1: Composer (Recommended)

```bash
composer require blep/blep
```

Or install globally:

```bash
composer global require blep/blep
```

### Option 2: Single-file phar download

```bash
curl -O https://github.com/yourusername/blep/releases/latest/download/bldoc
chmod +x bldoc
```

### Option 3: From source

```bash
git clone https://github.com/yourusername/blep.git
cd blep
composer install
composer build  # Creates single-file phar in build/
```

## Quick Start

1. **Add tags to your PHP code:**
   ```php
   // In OrderService.php
   /**
    * @bl-topic Order Processing
    * @bl-detail Orders over $1000 require manager approval
    */
   
   // In PaymentGateway.php - same topic, different file
   /**
    * @bl-topic Order Processing
    * @bl-detail Payment must be authorized before order enters processing
    */
   ```

2. **Generate documentation:**
   ```bash
   ./vendor/bin/bldoc src/
   ```

3. **Browse your docs:**
   ```bash
   open bl-docs/index.html
   ```
   
   Topics from multiple files are automatically merged into a single documentation page.

## Usage

### Basic

```bash
bldoc src/
```

### With options

```bash
# Custom output directory and title
bldoc -o docs/ -t "My Project Docs" src/

# Exclude directories
bldoc --exclude vendor/ --exclude tests/ .

# Verbose output
bldoc -v src/ lib/
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

Defines a top-level documentation topic. Topics with the same name across multiple files are automatically merged into a single documentation page.

```php
// In OrderService.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Order Validation
 * @bl-detail Orders over $1000 require manager approval
 */
class OrderService { }

// In PaymentGateway.php - adds to the same "Order Processing" topic
/**
 * @bl-topic Order Processing
 * @bl-subtopic Payment Requirements
 * @bl-detail Payment must be authorized before order enters processing
 */
class PaymentGateway { }
```

Both files contribute to a single "Order Processing" documentation page with two subtopics. Each detail shows its source file and line number.

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

### `@bl-rationale`

Documents the reasoning behind a business rule.

```php
/**
 * @bl-topic Order Processing
 * @bl-rationale We require manager approval for large orders to prevent fraud
 * @bl-detail Orders over $1000 require manager approval
 */
```

The rationale appears in a collapsible "Why?" section next to the detail.

### `@bl-see`

Creates cross-references to related topics.

```php
/**
 * @bl-see Payment Processing
 * @bl-see Inventory Management: Stock Reservation
 */
```

## Multi-File Topics

Business logic for a single concept is often scattered across multiple files. Blep automatically merges topics with the same name into a single documentation page.

**Example:** The "Order Processing" topic might span:
- `OrderService.php` — validation and workflow rules
- `PaymentGateway.php` — payment requirements
- `ShippingCalculator.php` — shipping requirements

All three files contribute to one "Order Processing" documentation page. Each detail shows its source file and line number for traceability.

```php
// OrderService.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Order Validation
 * @bl-detail Orders over $1000 require manager approval
 */

// PaymentGateway.php - adds to same topic
/**
 * @bl-topic Order Processing
 * @bl-subtopic Payment Requirements
 * @bl-detail Payment must be authorized before order enters processing
 */
```

Result: One `topic-order-processing.html` page with both subtopics.

See `example/output/topic-order-processing-EXAMPLE.html` for a complete demonstration.

## Example Output

The tool generates:

- `bl-docs/index.html` — Lists all topics with links to search and changelog
- `bl-docs/topic-*.html` — Individual topic pages with inline history and rationale
- `bl-docs/search.html` — Interactive search interface powered by Fuse.js
- `bl-docs/search-index.json` — Search index for client-side searching
- `bl-docs/changelog.html` — Timeline of all business rule changes
- `bl-docs/*.md` — Markdown versions of all documentation

Each detail includes:
- Source file and line number for easy reference
- Last modified date and author (from git blame)
- Recent change history (last 5 commits affecting that line)
- Rationale in collapsible "Why?" sections
- Code snippets in collapsible sections

## Requirements

- PHP 7.4 or higher
- No PHP extensions required beyond standard installation

## Development

### Setup

```bash
git clone https://github.com/yourusername/blep.git
cd blep
composer install
```

### Building

```bash
composer build
# or
./vendor/bin/box compile
```

Creates `build/bldoc` — a single-file phar executable.

### Testing

```bash
# Run test suite
composer test
# or
./vendor/bin/pest

# Test on example project
./bin/bldoc -v example/src/
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