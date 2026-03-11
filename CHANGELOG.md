# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-03-11

### Changed
- **BREAKING**: Restructured project to follow enterprise PHP standards
- **BREAKING**: Moved from flat file structure to PSR-4 namespaced architecture
- **BREAKING**: Changed CLI entry point from `bl-doc-gen.php` to `bin/bldoc`
- Migrated from custom test runner to Pest testing framework
- Replaced custom build script with Box for phar generation
- Updated Composer configuration with PSR-4 autoloading

### Added
- PSR-4 autoloading with `Blep\` namespace
- Organized source structure: `src/Parser/`, `src/Generator/`, `src/Command/`
- Pest test framework integration
- Box configuration for professional phar builds
- PHPUnit XML configuration
- Comprehensive test suite with Pest
- Updated documentation reflecting new structure

### Removed
- Removed temporary markdown files (CHECKLIST.md, IMPLEMENTATION_COMPLETE.md, FEATURES.md, AGENTS.md)
- Removed old `bl-doc-gen.php` entry point
- Removed old `build.sh` script
- Removed custom test runner (`tests/run-tests.php`)

### Fixed
- Improved .gitignore to properly exclude generated files and vendor directory

## [1.0.0] - 2026-03-09

### Added
- Initial release
- Parser for `@bl-topic`, `@bl-subtopic`, `@bl-detail`, and `@bl-see` tags
- HTML generator with embedded CSS
- CLI interface with argument parsing
- Single-file distributable build script
- Recursive directory scanning
- Exclude pattern support
- Verbose mode
- Warning system for orphaned tags
- Cross-reference support between topics
- Source file and line number tracking

### Features
- PHP 7.4+ compatibility
- Zero external dependencies
- Self-contained HTML output
- Responsive documentation site
