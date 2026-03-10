#!/bin/bash

# Simple test script for bl-doc-gen

set -e

echo "=== Testing bl-doc-gen ==="
echo ""

# Test 1: Help output
echo "Test 1: Help output"
php bl-doc-gen.php --help > /dev/null
echo "✓ Help works"
echo ""

# Test 2: Version output
echo "Test 2: Version output"
php bl-doc-gen.php --version
echo "✓ Version works"
echo ""

# Test 3: Generate docs from example
echo "Test 3: Generate documentation from example.php"
rm -rf test-output
php bl-doc-gen.php -o test-output -t "Test Docs" example.php
echo "✓ Generation works"
echo ""

# Test 4: Check output files exist
echo "Test 4: Verify output files"
if [ ! -f "test-output/index.html" ]; then
    echo "✗ index.html not found"
    exit 1
fi
echo "✓ index.html exists"

if [ ! -f "test-output/topic-order-processing.html" ]; then
    echo "✗ topic-order-processing.html not found"
    exit 1
fi
echo "✓ Topic pages exist"
echo ""

# Test 5: Test single-file version
echo "Test 5: Test single-file version"
rm -rf test-output-2
./bldoc -o test-output-2 example.php
if [ ! -f "test-output-2/index.html" ]; then
    echo "✗ Single-file version failed"
    exit 1
fi
echo "✓ Single-file version works"
echo ""

# Test 6: Test verbose mode
echo "Test 6: Test verbose mode"
php bl-doc-gen.php -v -o test-output-3 example.php | grep -q "Parsing:"
echo "✓ Verbose mode works"
echo ""

# Test 7: Test exclude pattern
echo "Test 7: Test exclude pattern"
php bl-doc-gen.php -o test-output-4 --exclude example.php . 2>&1 | grep -q "Files scanned: 0"
echo "✓ Exclude pattern works"
echo ""

# Cleanup
rm -rf test-output test-output-2 test-output-3 test-output-4

echo "=== All tests passed! ==="
