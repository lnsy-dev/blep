# Multi-File Topics

One of Blep's key features is the ability to automatically merge business logic documentation from multiple source files into a single, cohesive topic page.

## The Problem

In real-world applications, business logic for a single concept is often scattered across multiple files:

- **Order Processing** might span `OrderService.php`, `PaymentGateway.php`, and `ShippingCalculator.php`
- **User Authentication** could involve `AuthController.php`, `SessionManager.php`, and `PermissionChecker.php`
- **Inventory Management** might touch `StockTracker.php`, `WarehouseService.php`, and `ReorderService.php`

Traditional documentation approaches force you to either:
1. Duplicate documentation across files (hard to maintain)
2. Document everything in one place (disconnected from code)
3. Create separate doc pages per file (fragmented understanding)

## The Solution

Blep automatically merges topics with the same name into a single documentation page, regardless of which files they appear in.

## How It Works

### Step 1: Annotate Multiple Files

```php
// File: src/Orders/OrderService.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Order Validation
 * @bl-detail Orders over $1000 require manager approval
 * @bl-rationale Prevents fraud and ensures oversight on large transactions
 */
class OrderService {
    public function validateOrder($order) {
        // validation logic
    }
}
```

```php
// File: src/Payment/PaymentGateway.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Payment Requirements
 * @bl-detail Payment must be authorized before order enters processing
 * @bl-rationale Ensures funds are available before committing inventory
 */
class PaymentGateway {
    public function authorizePayment($amount) {
        // payment logic
    }
}
```

```php
// File: src/Shipping/ShippingCalculator.php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Shipping Requirements
 * @bl-detail Free shipping applies to orders over $50
 * @bl-rationale Incentivizes larger orders and improves customer satisfaction
 */
class ShippingCalculator {
    public function calculateShipping($order) {
        // shipping logic
    }
}
```

### Step 2: Generate Documentation

```bash
./blep src/
```

### Step 3: View Merged Output

Blep generates a single file: `bl-docs/topic-order-processing.html`

This page contains:
- **Order Validation** subtopic (from OrderService.php)
- **Payment Requirements** subtopic (from PaymentGateway.php)
- **Shipping Requirements** subtopic (from ShippingCalculator.php)

Each detail includes:
- The business rule text
- Source file name and line number
- Last modified date and author (from VCS)
- Recent change history
- Rationale in collapsible "Why?" sections
- Code snippets in collapsible sections

## Benefits

### 1. Document Where You Code

Write documentation directly in the file where the logic lives. No need to maintain separate documentation files or remember to update multiple locations.

### 2. Unified View for Stakeholders

Non-technical stakeholders get a single, comprehensive page for each business concept, even though the implementation spans multiple files.

### 3. Traceability

Every detail shows its source file and line number, so developers can quickly jump to the implementation.

### 4. Natural Organization

Organize your code by technical concerns (services, controllers, models) while organizing your documentation by business concerns (order processing, user management, inventory).

### 5. Easy Maintenance

When you modify business logic in any file, the documentation stays with the code. Run `blep` again to regenerate the merged documentation.

## Advanced Usage

### Cross-File References

Use `@bl-see` to link related topics, even across files:

```php
// In OrderService.php
/**
 * @bl-topic Order Processing
 * @bl-see Payment Processing
 * @bl-see Inventory Management: Stock Reservation
 */
```

### Subtopic Organization

Use subtopics to organize details within a merged topic:

```php
// Multiple files can contribute to the same subtopic
// File 1:
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 * @bl-detail Check customer credit limit
 */

// File 2:
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 * @bl-detail Verify product availability
 */
```

Both details appear under the same "Validation Rules" subtopic.

### Version Control History

Each detail shows its individual change history, even though they're merged into one page:

- Details from `OrderService.php` show commits affecting that file
- Details from `PaymentGateway.php` show commits affecting that file
- All appear together on the same topic page

## Example Output Structure

```
Order Processing
├── Order Validation (from OrderService.php)
│   ├── Orders over $1000 require manager approval
│   │   ├── Source: OrderService.php:42
│   │   ├── Author: John Doe (2024-03-10)
│   │   ├── History: [last 5 commits]
│   │   └── Rationale: Prevents fraud...
│   └── ...
├── Payment Requirements (from PaymentGateway.php)
│   ├── Payment must be authorized before processing
│   │   ├── Source: PaymentGateway.php:78
│   │   ├── Author: Jane Smith (2024-03-08)
│   │   ├── History: [last 5 commits]
│   │   └── Rationale: Ensures funds...
│   └── ...
└── Shipping Requirements (from ShippingCalculator.php)
    ├── Free shipping over $50
    │   ├── Source: ShippingCalculator.php:23
    │   ├── Author: Bob Johnson (2024-03-05)
    │   ├── History: [last 5 commits]
    │   └── Rationale: Incentivizes...
    └── ...
```

## Best Practices

### 1. Use Consistent Topic Names

Make sure topic names are identical across files (case-sensitive):

✅ Good:
```php
// @bl-topic Order Processing
// @bl-topic Order Processing
```

❌ Bad:
```php
// @bl-topic Order Processing
// @bl-topic order processing  (different case)
```

### 2. Use Subtopics for Organization

Within a merged topic, use subtopics to group related details:

```php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 * @bl-detail ...
 */

/**
 * @bl-topic Order Processing
 * @bl-subtopic Payment Rules
 * @bl-detail ...
 */
```

### 3. Add Cross-References

Help readers navigate between related topics:

```php
/**
 * @bl-topic Order Processing
 * @bl-see Payment Processing
 * @bl-see Inventory Management
 * @bl-see Customer Accounts: Credit Limits
 */
```

### 4. Include Rationale

Explain why rules exist, especially for complex business logic:

```php
/**
 * @bl-topic Order Processing
 * @bl-rationale We require manager approval for large orders to prevent fraud
 * @bl-detail Orders over $1000 require manager approval
 */
```

## Real-World Example

See `example/output/topic-order-processing-EXAMPLE.html` in the Blep repository for a complete demonstration of multi-file topic merging with:
- Multiple source files
- Various subtopics
- Cross-references
- Rationale sections
- Version control history
- Code snippets

## Technical Details

### Topic Merging Algorithm

1. Blep scans all PHP files in the specified directory
2. Extracts all `@bl-topic` annotations
3. Groups details by topic name (case-sensitive)
4. Within each topic, groups by subtopic
5. Preserves source file and line number for each detail
6. Generates a single HTML file per unique topic name

### File Naming

Topic pages are named using slugified topic names:
- "Order Processing" → `topic-order-processing.html`
- "User Authentication" → `topic-user-authentication.html`
- "Inventory Management: Stock" → `topic-inventory-management-stock.html`

### Search Integration

The search index includes all details from all files, making it easy to find specific business rules regardless of which file they're in.
