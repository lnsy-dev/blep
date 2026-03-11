# Project Structure

Blep follows standard enterprise PHP project conventions with PSR-4 autoloading.

## Directory Layout

```
blep/
├── bin/
│   └── bldoc                    # CLI entry point
├── src/
│   ├── Parser/
│   │   └── BLParser.php         # Parses @bl-* tags from PHP files
│   ├── Generator/
│   │   ├── HtmlGenerator.php    # Generates HTML documentation
│   │   ├── MarkdownGenerator.php # Generates Markdown documentation
│   │   └── SearchIndexGenerator.php # Generates search index JSON
│   └── Command/
│       └── GenerateCommand.php  # CLI command logic
├── tests/
│   ├── Pest.php                 # Pest configuration
│   ├── Unit/                    # Unit tests
│   └── fixtures/                # Test fixtures
├── example/
│   ├── src/                     # Example PHP files with @bl-* tags
│   └── generate.sh              # Script to generate example docs
├── docs/
│   ├── quickstart.md            # Quick start guide
│   ├── cli-reference.md         # CLI reference
│   ├── project-structure.md     # This file
│   └── new-features-guide.md    # New features documentation
├── .github/
│   ├── CONTRIBUTING.md          # Contribution guidelines
│   ├── pull_request_template.md # PR template
│   └── ISSUE_TEMPLATE/          # Issue templates
├── composer.json                # Composer configuration with PSR-4
├── box.json                     # Box configuration for phar build
├── README.md                    # Main documentation
├── LICENSE                      # MIT license
└── CHANGELOG.md                 # Version history
```

## Generated Output (gitignored)

```
bl-docs/                         # Generated documentation
├── index.html                   # Topic index
├── topic-*.html                 # Individual topic pages
├── search.html                  # Search interface
├── search-index.json            # Search index
├── changelog.html               # Change history
└── *.md                         # Markdown versions
```

## Namespace Structure

All classes use the `Blep\` namespace with PSR-4 autoloading:

- `Blep\Parser\BLParser` - Main parser class
- `Blep\Generator\HtmlGenerator` - HTML output generator
- `Blep\Generator\MarkdownGenerator` - Markdown output generator
- `Blep\Generator\SearchIndexGenerator` - Search index generator
- `Blep\Command\GenerateCommand` - CLI command handler

## Key Files

### `bin/bldoc`
Thin CLI wrapper that loads the Composer autoloader and executes `GenerateCommand`.

### `src/Parser/BLParser.php`
Scans PHP files for `@bl-*` tags and builds an internal data structure. Integrates with git for blame and history information.

### `src/Generator/HtmlGenerator.php`
Generates responsive HTML documentation with search, changelog, and topic pages.

### `src/Command/GenerateCommand.php`
Handles CLI argument parsing, orchestrates the parsing and generation process, and provides user feedback.

## Development Workflow

1. **Install dependencies**: `composer install`
2. **Run tests**: `composer test` or `./vendor/bin/pest`
3. **Build phar**: `composer build` or `./vendor/bin/box compile`
4. **Generate docs**: `./bin/bldoc example/src/`

## Testing

Tests are written using Pest and located in `tests/`. Run with:

```bash
./vendor/bin/pest
```

Test fixtures are in `tests/fixtures/` and contain sample PHP files with various `@bl-*` tag patterns.

## Building

The project uses Box to create a single-file phar executable:

```bash
./vendor/bin/box compile
```

This creates `build/bldoc` which can be distributed as a standalone executable.
