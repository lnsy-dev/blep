# Quick Start Guide

## Installation

### Option 1: Composer (Recommended)

```bash
composer global require blep/blep
```

Then use `bldoc` from anywhere:

```bash
bldoc src/
```

### Option 2: Download single-file

```bash
curl -O https://raw.githubusercontent.com/yourusername/blep/main/bldoc
chmod +x bldoc
```

### Option 3: Build from source

```bash
git clone https://github.com/yourusername/blep.git
cd blep
./build.sh
```

## Basic Usage

1. Add business logic tags to your PHP code:

```php
<?php

/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 * @bl-detail Orders over $1000 require manager approval
 */
class OrderService {
    public function createOrder($data) {
        // @bl-detail Inventory is reserved for 15 minutes
        $this->reserveInventory($data['items']);
    }
}
```

2. Generate documentation:

```bash
./bldoc src/
```

3. Open the generated site:

```bash
open bl-docs/index.html
```

## Common Patterns

### Document a workflow

```php
/**
 * @bl-topic User Registration
 * @bl-subtopic Email Verification
 * @bl-detail Verification email sent immediately after registration
 * @bl-detail Link expires after 24 hours
 * @bl-detail Users can request new link up to 3 times
 */
```

### Cross-reference related topics

```php
/**
 * @bl-topic Payment Processing
 * @bl-detail Payment must be authorized before order confirmation
 * @bl-see Order Processing: Payment Validation
 * @bl-see Fraud Detection
 */
```

### Document inline business rules

```php
public function calculateDiscount($order) {
    // @bl-detail VIP customers get 10% discount on all orders
    if ($order->customer->isVIP()) {
        return $order->total * 0.10;
    }
    
    // @bl-detail Orders over $100 get 5% discount
    if ($order->total > 100) {
        return $order->total * 0.05;
    }
}
```

## Tips

- Use descriptive topic names that match your domain language
- Keep each detail to a single rule or constraint
- Add tags as you write code, not as an afterthought
- Use `@bl-see` to show relationships between topics
- Run with `-v` flag to see which files are being processed

## Troubleshooting

**No tags found?**
- Make sure you're using `@bl-topic`, `@bl-subtopic`, `@bl-detail`, or `@bl-see`
- Check that tags are in docblocks (`/** */`) or inline comments (`//`)
- Run with `-v` to see which files are being scanned

**Warnings about orphaned tags?**
- Every `@bl-subtopic` and `@bl-detail` must come after a `@bl-topic`
- Make sure your topic declarations are in the right order

**Need to exclude directories?**
- Use `--exclude vendor/ --exclude tests/` to skip directories
- Can be specified multiple times
