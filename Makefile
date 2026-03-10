.PHONY: build test clean help example

help:
	@echo "Business Logic Documentation Generator"
	@echo ""
	@echo "Available targets:"
	@echo "  build    - Build single-file distributable (bldoc)"
	@echo "  test     - Run test suite"
	@echo "  example  - Generate docs from example.php"
	@echo "  clean    - Remove generated files"
	@echo "  help     - Show this help message"

build:
	@./build.sh

test:
	@./test.sh

example:
	@echo "Generating documentation from example.php..."
	@php bl-doc-gen.php -v -o bl-docs example.php
	@echo ""
	@echo "Open: bl-docs/index.html"

clean:
	@echo "Cleaning generated files..."
	@rm -rf bldoc bl-docs/ test-output*/
	@echo "Done"
