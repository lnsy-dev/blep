#!/bin/bash

# Generate business logic documentation for example e-commerce project

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="$SCRIPT_DIR/output"

echo "Generating business logic documentation..."
echo ""

# Run the generator
php "$SCRIPT_DIR/../bl-doc-gen.php" -o "$OUTPUT_DIR" -t "E-Commerce Business Rules" "$SCRIPT_DIR/src/"

if [ $? -eq 0 ]; then
    echo ""
    echo "✓ Documentation generated successfully!"
    echo "  Output: $OUTPUT_DIR"
    echo ""
    echo "Open in browser:"
    echo "  file://$OUTPUT_DIR/index.html"
    echo ""
    
    # Try to open in browser (works on most systems)
    if command -v xdg-open &> /dev/null; then
        xdg-open "$OUTPUT_DIR/index.html"
    elif command -v open &> /dev/null; then
        open "$OUTPUT_DIR/index.html"
    fi
else
    echo "✗ Documentation generation failed"
    exit 1
fi
