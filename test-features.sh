#!/bin/bash

echo "Testing new features..."
echo ""

# Generate documentation
php bl-doc-gen.php -o test-docs/ -v example/src/

echo ""
echo "Checking generated files..."
ls -lh test-docs/

echo ""
echo "Checking for new files:"
[ -f test-docs/search.html ] && echo "✓ search.html created" || echo "✗ search.html missing"
[ -f test-docs/search-index.json ] && echo "✓ search-index.json created" || echo "✗ search-index.json missing"
[ -f test-docs/changelog.html ] && echo "✓ changelog.html created" || echo "✗ changelog.html missing"

echo ""
echo "Sample search index entries:"
head -n 30 test-docs/search-index.json

echo ""
echo "Done! Open test-docs/index.html to browse the documentation."
echo "Try the search at test-docs/search.html"
