#!/bin/bash

# Demo script showing the complete workflow

echo "╔════════════════════════════════════════════════════════════╗"
echo "║  Blep - Change History & Search Features Demo             ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

echo "📝 Step 1: Showing example code with @bl-rationale..."
echo ""
cat << 'EOF'
<?php
/**
 * @bl-topic Payment Processing
 * @bl-subtopic Refund Rules
 * @bl-rationale We allow full refunds within 30 days to build customer trust
 * @bl-detail Full refunds available within 30 days of purchase
 * @bl-detail Partial refunds require manager approval
 */
class RefundService {
    public function processRefund($order) {
        // Implementation
    }
}
EOF

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "🔨 Step 2: Generating documentation..."
echo ""
php bl-doc-gen.php -o bl-docs/ example/src/

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "📂 Step 3: Generated files..."
echo ""
ls -1 bl-docs/*.html bl-docs/*.json 2>/dev/null | while read file; do
    basename "$file"
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "🔍 Step 4: Search index sample..."
echo ""
if [ -f bl-docs/search-index.json ]; then
    echo "First entry from search-index.json:"
    head -n 15 bl-docs/search-index.json
    echo "  ..."
    echo ""
    echo "Total entries: $(grep -o '"id":' bl-docs/search-index.json | wc -l)"
else
    echo "⚠️  search-index.json not found"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "✨ Features Available:"
echo ""
echo "  📖 Topic Pages"
echo "     • View business rules organized by topic"
echo "     • See inline change history (last 5 commits)"
echo "     • Click 'Why?' to see rationale"
echo "     • View code snippets"
echo ""
echo "  🔍 Interactive Search"
echo "     • Real-time fuzzy search"
echo "     • Search topics, details, code, authors"
echo "     • Card-based results with previews"
echo "     • Press '/' to focus search"
echo ""
echo "  📋 Changelog"
echo "     • Timeline of all changes"
echo "     • See who changed what and when"
echo "     • Click to view in context"
echo "     • Last 100 changes shown"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "🌐 Open in browser:"
echo ""
echo "  Index:     file://$(pwd)/bl-docs/index.html"
echo "  Search:    file://$(pwd)/bl-docs/search.html"
echo "  Changelog: file://$(pwd)/bl-docs/changelog.html"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "💡 Try these searches:"
echo ""
echo "  • 'approval'    - Find approval requirements"
echo "  • 'fraud'       - Find fraud prevention rules"
echo "  • 'refund'      - Find refund policies"
echo "  • '$1000'       - Find monetary thresholds"
echo "  • 'manager'     - Find manager-related rules"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "✅ Demo complete!"
echo ""
echo "Next steps:"
echo "  1. Open bl-docs/index.html in your browser"
echo "  2. Click '🔍 Search' to try the search feature"
echo "  3. Click '📋 Changelog' to see change history"
echo "  4. Browse topics and click 'Why?' buttons"
echo ""
