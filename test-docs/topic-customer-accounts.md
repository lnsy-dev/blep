# Customer Accounts

[← Back to Index](index.md)

## Account Creation

*CustomerAccount.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Email must be unique across all accounts
- Password must be at least 12 characters
- Password must contain uppercase, lowercase, number, and symbol
- Disposable email domains are blocked

```php
class CustomerAccount {
    
    /**
     * @bl-subtopic Account Verification
     * @bl-detail Verification email sent upon registration
     * @bl-detail Verification link expires after 24 hours
     * @bl-detail Unverified accounts can browse but not purchase
     * @bl-detail Users can request new verification email up to 3 times
     */
    public function createAccount($email, $password) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Loyalty Tier Benefits
     * @bl-detail Bronze tier: 0-999 points, standard benefits
     * @bl-detail Silver tier: 1,000-4,999 points, 5% discount on all orders
     * @bl-detail Gold tier: 5,000+ points, 10% discount + free express shipping
     * @bl-detail Tier status recalculated monthly based on trailing 12-month points
     * @bl-see Discount Rules: Loyalty Program
     */
    public function calculateTier($customer) {
        // Implementation
    }
}
```

## Account Verification

*CustomerAccount.php:15 — last updated 2026-03-10 01:25 by lnsy*

- Verification email sent upon registration
- Verification link expires after 24 hours
- Unverified accounts can browse but not purchase
- Users can request new verification email up to 3 times

```php
    public function createAccount($email, $password) {
        // Implementation
    }
```

## Loyalty Tier Benefits

*CustomerAccount.php:26 — last updated 2026-03-10 01:25 by lnsy*

- Bronze tier: 0-999 points, standard benefits
- Silver tier: 1,000-4,999 points, 5% discount on all orders
- Gold tier: 5,000+ points, 10% discount + free express shipping
- Tier status recalculated monthly based on trailing 12-month points
- See: [Discount Rules → Loyalty Program](topic-discount-rules.md#loyalty-program)

```php
    public function calculateTier($customer) {
        // Implementation
    }
```

---
*Generated on 2026-03-10 02:06:49*
