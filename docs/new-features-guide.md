# Quick Start Guide - New Features

## 1. Multi-File Topics

Business logic for a single concept often spans multiple files. Blep automatically merges topics with the same name into one documentation page.

```php
// OrderService.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Order Validation
 * @bl-detail Orders over $1000 require manager approval
 */

// PaymentGateway.php - adds to same "Order Processing" topic
/**
 * @bl-topic Order Processing
 * @bl-subtopic Payment Requirements
 * @bl-detail Payment must be authorized before order enters processing
 */
```

**Result**: One `topic-order-processing.html` page with both subtopics. Each detail shows its source file and line number.

See [Multi-File Topics Guide](multi-file-topics.md) for complete documentation.

## 2. Version Control Support

Blep automatically detects your VCS and extracts change history:

- **Git** - Detected via `.git` directory
- **Subversion (SVN)** - Detected via `.svn` directory
- **Perforce (P4)** - Detected via `p4 info` command

No configuration needed - just run `blep` and it will detect your VCS automatically.

See [VCS Support Guide](vcs-support.md) for implementation details.

## 3. Using @bl-rationale

Add rationale before or with your business rules:

```php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Fraud Detection
 * @bl-rationale High-value orders to new customers have higher fraud risk
 * @bl-detail Orders over $1000 to new customers require phone verification
 */
```

**Result**: A collapsible "Why?" button appears next to the detail in the generated docs.

## 12. Viewing Change History

When you open a topic page, you'll see:

```
OrderService.php:15 — last updated 2026-03-09 19:00 by John Doe

▸ Recent Changes
  (Click to expand and see last 5 commits)
```

Expanded view shows:
```
▾ Recent Changes
  • a1b2c3d 2026-03-09 19:00 by John Doe — Updated fraud threshold
  • d4e5f6g 2026-03-01 14:30 by Jane Smith — Added phone verification
  • g7h8i9j 2026-02-15 10:15 by John Doe — Initial implementation
```

**Note**: History is available when using Git, Subversion, or Perforce. Blep automatically detects your VCS.

## 11. Using Search

### Access Search
- Click "🔍 Search" in navigation on any page
- Or press `/` keyboard shortcut (when not in an input field)

### Search Features
- **Real-time results** as you type
- **Fuzzy matching** finds results even with typos
- **Searches everything**: topics, details, code, authors, commit messages

### Search Results Display

Each result card shows:
```
┌─────────────────────────────────────────────────┐
│ [Order Processing] Fraud Detection              │
│                                                  │
│ Orders over $1000 to new customers require      │
│ phone verification                              │
│                                                  │
│ Why: High-value orders to new customers have    │
│      higher fraud risk                          │
│                                                  │
│ ┌─────────────────────────────────────────────┐ │
│ │ public function checkFraud($order) {        │ │
│ │     if ($order->total > 1000) {             │ │
│ │         // verification logic                │ │
│ └─────────────────────────────────────────────┘ │
│                                                  │
│ OrderService.php:25 • John Doe                  │
│ View in context →                               │
└─────────────────────────────────────────────────┘
```

### Example Searches
- `"fraud"` - Find all fraud-related rules
- `"manager approval"` - Find approval requirements
- `"John Doe"` - Find rules last modified by John
- `"$1000"` - Find rules mentioning specific amounts
- `"verification"` - Find verification requirements

## 12. Using Changelog

### Access Changelog
- Click "📋 Changelog" in navigation on any page

### What You See
A timeline of recent changes across ALL topics:

```
2026-03-09 19:00  a1b2c3d  John Doe
Order Processing → Fraud Detection
Orders over $1000 to new customers require phone verification
Updated fraud threshold

2026-03-01 14:30  d4e5f6g  Jane Smith
Payment Processing → Refund Rules
Full refunds available within 30 days of purchase
Added refund policy

2026-02-15 10:15  g7h8i9j  John Doe
Discount Rules → Coupon Codes
Only one coupon code per order
Initial implementation
```

Each entry is clickable and takes you to the relevant topic page.

## 11. Navigation Flow

```
Index Page
├── 🔍 Search ──────┐
├── 📋 Changelog ───┤
└── Topics         │
    ├── Topic 1 ───┤
    ├── Topic 2 ───┤
    └── Topic 3 ───┘
         │
         └── ▸ Recent Changes (collapsible)
         └── Details with "Why?" buttons
```

All pages have consistent navigation:
- **Index**: Search | Changelog
- **Topics**: ← Back to Index | Search | Changelog  
- **Search**: ← Back to Index | Changelog
- **Changelog**: ← Back to Index | Search

## 12. For Business Users

### Finding Information
1. **Browse by topic** - Start at index.html
2. **Search for keywords** - Use search.html when you know what you're looking for
3. **See recent changes** - Check changelog.html to stay updated

### Understanding Rules
- Click "Why?" buttons to see rationale
- Check "Recent Changes" to see rule evolution
- View code snippets to see implementation

### Communicating with Developers
- Reference specific rules by file:line (e.g., "OrderService.php:25")
- Share search results by copying URLs
- Discuss rationale to understand trade-offs

## 11. For Developers

### Documenting Rules
```php
/**
 * @bl-topic [Business Area]
 * @bl-subtopic [Specific Feature]
 * @bl-rationale [Why this rule exists]
 * @bl-detail [What the rule is]
 * @bl-see [Related Topic]: [Related Subtopic]
 */
```

### Best Practices
1. **Always add rationale** for non-obvious rules
2. **Update docs when changing logic** (git will track it)
3. **Link related topics** with @bl-see
4. **Use clear, business-friendly language**

### Generating Docs
```bash
# Standard generation
php bl-doc-gen.php src/

# With options
php bl-doc-gen.php -o docs/ -t "My Project" src/

# Verbose mode
php bl-doc-gen.php -v src/
```

## 12. Tips & Tricks

### Search Tips
- Use quotes for exact phrases: `"manager approval"`
- Search by author to find who wrote what
- Search code snippets to find implementation details
- Use partial words: `"verif"` finds "verification", "verify", etc.

### History Tips
- History only works in git repositories
- Commit messages appear in search and changelog
- Write clear commit messages for better documentation

### Rationale Tips
- Explain business context, not technical details
- Answer "why" not "how"
- Reference regulations, policies, or business goals
- Keep it concise (1-2 sentences)

## 11. Troubleshooting

### No History Showing
- Make sure your project is in a git repository
- Run `git init` if needed
- Commit your files with `git add` and `git commit`

### Search Not Working
- Check browser console for errors
- Ensure search-index.json was generated
- Try refreshing the page
- Make sure JavaScript is enabled

### Rationale Not Appearing
- Ensure @bl-rationale comes before @bl-detail
- Check that tags are in docblock comments (/** */)
- Regenerate docs after adding rationale

## 12. Examples

See `example/src/` directory for complete examples:
- `OrderService.php` - Multiple subtopics, cross-references
- `RefundService.php` - Rationale examples
- `DiscountEngine.php` - Complex rules with rationale
- `PaymentGateway.php` - Integration examples

Generate example docs:
```bash
php bl-doc-gen.php -o example-docs/ example/src/
open example-docs/index.html
```
