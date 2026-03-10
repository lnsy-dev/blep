# Contributing to Blep

Thank you for your interest in contributing to Blep!

## Development Setup

1. Clone the repository
2. Ensure you have PHP 7.4+ installed
3. Run tests: `php tests/run-tests.php`
4. Build single-file version: `./build.sh`

## Code Style

- Follow PSR-12 coding standards
- Keep functions focused and minimal
- Add comments for business logic
- Use meaningful variable names

## Testing

- Test your changes with the example project: `./bldoc example/src/`
- Run the test suite: `php tests/run-tests.php`
- Test edge cases with various @bl-* tag combinations

## Submitting Changes

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make your changes
4. Test thoroughly
5. Commit with clear messages
6. Push to your fork
7. Create a pull request

## Reporting Issues

- Use the issue templates provided
- Include PHP version and OS information
- Provide sample code that reproduces the issue
- Be clear and concise in your description

## Questions?

Open an issue for discussion before starting major changes.