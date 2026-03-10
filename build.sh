#!/bin/bash

# Build script to create single-file distributable version

OUTPUT="bldoc"
TEMP_FILE="${OUTPUT}.tmp"

echo "Building single-file distributable: $OUTPUT"

# Start with shebang
echo '#!/usr/bin/env php' > "$TEMP_FILE"
echo '<?php' >> "$TEMP_FILE"
echo '' >> "$TEMP_FILE"

# Add version constant
grep "^const VERSION" bl-doc-gen.php >> "$TEMP_FILE"
echo '' >> "$TEMP_FILE"

# Add BLParser class (strip opening <?php tag)
echo "// === BLParser ===" >> "$TEMP_FILE"
tail -n +2 BLParser.php >> "$TEMP_FILE"
echo '' >> "$TEMP_FILE"

# Add BLHtmlGenerator class (strip opening <?php tag)
echo "// === BLHtmlGenerator ===" >> "$TEMP_FILE"
tail -n +2 BLHtmlGenerator.php >> "$TEMP_FILE"
echo '' >> "$TEMP_FILE"

# Add main CLI logic (strip shebang, <?php, require statements, and VERSION constant)
echo "// === Main CLI ===" >> "$TEMP_FILE"
sed -e '1,/^const VERSION/d' -e '/^require_once/d' bl-doc-gen.php >> "$TEMP_FILE"

# Make executable
chmod +x "$TEMP_FILE"

# Move to final location
mv "$TEMP_FILE" "$OUTPUT"

echo "✓ Build complete: $OUTPUT"
echo "  Size: $(wc -c < "$OUTPUT") bytes"
echo "  Test with: ./$OUTPUT --help"
