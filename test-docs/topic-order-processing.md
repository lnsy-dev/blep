# Order Processing

[← Back to Index](index.md)

## Order Validation

*OrderService.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Minimum order value is $10.00
- Maximum order value is $50,000.00 without manual approval
- Orders over $1,000 require manager approval
- See: [Payment Processing → ](topic-payment-processing.md#)

```php
class OrderService {
    
    /**
     * @bl-subtopic Order Status Workflow
     * @bl-detail New orders start in "pending" status
     * @bl-detail Orders move to "processing" after payment confirmation
     * @bl-detail Orders move to "shipped" when carrier picks up package
     * @bl-detail Orders move to "delivered" when customer confirms receipt
     * @bl-detail Customers can cancel orders only in "pending" status
     */
    public function processOrder($order) {
        // @bl-detail Inventory is reserved for 15 minutes during checkout
        // @bl-detail If payment fails, inventory reservation is released
    }
    
    /**
     * @bl-subtopic Fraud Detection
     * @bl-detail Orders with mismatched billing/shipping addresses are flagged
     * @bl-detail More than 3 orders from same IP in 1 hour triggers review
     * @bl-detail High-value orders to new customers require phone verification
     * @bl-see Customer Accounts: Account Verification
     */
    public function checkFraud($order) {
        // Implementation
    }
}
```

## Order Status Workflow

*OrderService.php:15 — last updated 2026-03-10 01:25 by lnsy*

- New orders start in "pending" status
- Orders move to "processing" after payment confirmation
- Orders move to "shipped" when carrier picks up package
- Orders move to "delivered" when customer confirms receipt
- Customers can cancel orders only in "pending" status
- Inventory is reserved for 15 minutes during checkout
- If payment fails, inventory reservation is released

```php
    public function processOrder($order) {
        // @bl-detail Inventory is reserved for 15 minutes during checkout
        // @bl-detail If payment fails, inventory reservation is released
    }
```

## Fraud Detection

*OrderService.php:28 — last updated 2026-03-10 01:25 by lnsy*

- Orders with mismatched billing/shipping addresses are flagged
- More than 3 orders from same IP in 1 hour triggers review
- High-value orders to new customers require phone verification
- See: [Customer Accounts → Account Verification](topic-customer-accounts.md#account-verification)

```php
    public function checkFraud($order) {
        // Implementation
    }
```

---
*Generated on 2026-03-10 02:06:49*
