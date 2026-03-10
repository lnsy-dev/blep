# Agent Guide: Documenting Business Logic with Blep

This guide helps LLMs add business logic documentation to codebases using blep's annotation system.

## Overview

Blep extracts business logic documentation from code comments using `@bl-*` tags. Your task is to identify business rules, workflows, and domain logic in the code and document them inline.

## Before You Start

**Use git to understand the code:**

```bash
# See what files have changed frequently (likely contain important logic)
git log --pretty=format: --name-only | sort | uniq -c | sort -rg | head -20

# Check commit messages for business context
git log --all --grep="business\|rule\|policy\|workflow" --oneline

# Look at recent changes to understand current focus
git log --since="3 months ago" --oneline

# See who wrote specific files (they may have documented rationale elsewhere)
git blame <file>
```

## Annotation Tags

### `@bl-topic`
Top-level category for related business logic. Use on classes or at file level.

```php
/**
 * @bl-topic Order Processing
 */
class OrderService {}
```

### `@bl-subtopic`
Subsection within a topic. Use for logical groupings.

```php
/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 */
```

### `@bl-detail`
Specific business rule or workflow step. This is the core documentation.

```php
/**
 * @bl-detail Orders over $1000 require manager approval
 */
public function createOrder($amount) {}

// Inline for specific logic
if ($amount > 1000) {
    // @bl-detail Manager approval required for orders exceeding $1000
    $this->requestApproval();
}
```

### `@bl-rationale`
Explains WHY a rule exists. Check git history for context.

```php
/**
 * @bl-detail Payment retries limited to 3 attempts
 * @bl-rationale Prevents excessive payment processor fees and potential fraud flags
 */
```

### `@bl-see`
Cross-reference related topics.

```php
/**
 * @bl-see Payment Processing
 * @bl-see Inventory Management: Stock Reservation
 */
```

## What to Document

**DO document:**
- Business rules and constraints (approval thresholds, validation rules)
- Workflow steps and state transitions
- Domain-specific calculations and formulas
- Integration points with external systems
- Data retention and compliance rules
- Edge cases with business implications

**DON'T document:**
- Implementation details (how code works)
- Standard programming patterns
- Obvious technical operations
- Framework-specific boilerplate

## Process

1. **Scan git history** for business context in commits
2. **Identify topics** by looking at class/module boundaries
3. **Extract rules** from conditionals, validations, and calculations
4. **Find rationale** in git commits, PRs, or ask the user
5. **Add cross-references** between related business areas
6. **Keep it concise** — one line per detail when possible

## Example

```php
/**
 * @bl-topic Subscription Management
 * @bl-subtopic Cancellation Policy
 * @bl-rationale Immediate cancellation caused support issues with partial refunds
 */
class SubscriptionService {
    /**
     * @bl-detail Cancellations take effect at end of billing period
     * @bl-detail Pro-rated refunds only available within 7 days of charge
     * @bl-see Billing: Refund Processing
     */
    public function cancel($subscriptionId) {
        // @bl-detail Grace period allows reactivation without re-onboarding
        $this->setStatus($subscriptionId, 'pending_cancellation');
    }
}
```

## Tips

- Check git blame to see when business logic was added
- Look for TODO/FIXME comments that explain business context
- Search for keywords: "must", "should", "require", "policy", "rule"
- Ask the user about unclear business rules rather than guessing
- Keep annotations close to the code they describe
