# Business Logic Documentation Generator

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

### Option 1: Single-file (Recommended)

Download the standalone `bldoc` file:

```bash
curl -O https://example.com/bldoc
chmod +x bldoc
```

### Option 2: From source

```bash
git clone https://github.com/yourusername/bl-doc-gen.git
cd bl-doc-gen
./build.sh  # Creates single-file 'bldoc'
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

## Tag Reference

### `@bl-topic`

Defines a top-level documentation topic. All subsequent `@bl-subtopic` and `@bl-detail` tags belong to this topic until a new `@bl-topic` is encountered.

```php
/**
 * @bl-topic Order Processing
 */
class OrderService {
    // ...
}
```

### `@bl-subtopic`

Creates a subsection within the current topic. Groups related details together.

```php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 */
```

### `@bl-detail` / `@bl-details`

Documents a specific business rule, constraint, or workflow step. Can be used in docblocks or inline comments.

```php
/**
 * @bl-detail Orders over $1000 require manager approval
 * @bl-detail Shipping address must be validated against USPS API
 */
public function createOrder($data) {
    // @bl-detail Inventory is reserved for 15 minutes during checkout
    $this->reserveInventory($data['items']);
}
```

### `@bl-see`

Creates a cross-reference to another topic or subtopic.

```php
/**
 * @bl-see Payment Processing
 * @bl-see Inventory Management: Stock Reservation
 */
```

Format: `@bl-see Topic Name` or `@bl-see Topic Name: Subtopic Name`

## Example

### Input (PHP file)

```php
<?php

/**
 * @bl-topic User Registration
 * @bl-subtopic Email Validation
 * @bl-detail Email must be unique across all users
 * @bl-detail Disposable email domains are blocked (see config/blocked-domains.txt)
 */
class UserRegistration {
    
    public function register($email, $password) {
        // @bl-detail Password must be at least 12 characters
        // @bl-detail Password must contain uppercase, lowercase, number, and symbol
        
        if (!$this->validatePassword($password)) {
            throw new ValidationException();
        }
        
        /**
         * @bl-subtopic Account Activation
         * @bl-detail Activation email expires after 24 hours
         * @bl-detail Users can request new activation email up to 3 times
         * @bl-see Email Delivery: Rate Limiting
         */
        $this->sendActivationEmail($email);
    }
}
```

### Output

The tool generates:

- `bl-docs/index.html` — Lists all topics
- `bl-docs/topic-user-registration.html` — Topic page with subtopics and details

Each detail includes the source file and line number for easy reference.

## Output Structure

```
bl-docs/
├── index.html                      # Topic index
├── topic-order-processing.html     # Individual topic pages
├── topic-user-registration.html
└── topic-payment-processing.html
```

The generated site is fully self-contained with embedded CSS. No external dependencies or build steps required.

## Requirements

- PHP 7.4 or higher
- No PHP extensions required beyond standard installation

## Development

### Project structure

```
bl-doc-gen/
├── BLParser.php           # Parses @bl-* tags from PHP files
├── BLHtmlGenerator.php    # Generates HTML documentation site
├── bl-doc-gen.php         # CLI entry point
├── build.sh               # Creates single-file distributable
└── README.md
```

### Building

```bash
./build.sh
```

Creates `bldoc` — a single-file version with all classes bundled.

### Testing

```bash
# Test on sample project
php bl-doc-gen.php -v examples/

# Test single-file version
./bldoc examples/
```

## Tips

1. **Organize by domain concepts** — Use topics like "Order Processing", "User Authentication", "Payment Rules" rather than technical class names
2. **Document the "why"** — Focus on business rules and constraints, not implementation details
3. **Keep details concise** — One rule per `@bl-detail` tag
4. **Use cross-references** — Link related topics with `@bl-see` to show relationships
5. **Update as you code** — Add tags when implementing new business logic

## Warnings

The tool will warn you about:

- `@bl-subtopic` without a preceding `@bl-topic`
- `@bl-detail` without a preceding `@bl-topic`
- `@bl-see` without a preceding `@bl-topic`
- Unreadable files or directories

These warnings don't stop generation but help you fix documentation structure.

## License

MIT License

Copyright (c) 2026

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

## Contributing

Contributions welcome! Please open an issue or pull request.

## Support

For bugs or feature requests, please open an issue on GitHub.
