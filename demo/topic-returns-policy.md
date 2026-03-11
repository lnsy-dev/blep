# Returns Policy

[← Back to Index](index.md)

## Return Window

*ReturnProcessor.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Standard return window is 30 days from delivery
- Electronics have 14-day return window
- Final sale items cannot be returned
- Opened software/media cannot be returned unless defective

```php
class ReturnProcessor {
    
    /**
     * @bl-subtopic Return Shipping
     * @bl-detail Customer pays return shipping unless item is defective
     * @bl-detail Defective items get prepaid return label
     * @bl-detail Return shipping cost is deducted from refund
     * @bl-see Shipping: Shipping Methods
     */
    public function initiateReturn($order) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Refund Processing
     * @bl-detail Refunds issued to original payment method
     * @bl-detail Refunds processed within 5-7 business days of receiving return
     * @bl-detail Restocking fee of 15% for non-defective electronics
     * @bl-detail Store credit issued immediately, no restocking fee
     */
    public function processRefund($return) {
        // Implementation
    }
}
```

## Return Shipping

*ReturnProcessor.php:15 — last updated 2026-03-10 01:25 by lnsy*

- Customer pays return shipping unless item is defective
- Defective items get prepaid return label
- Return shipping cost is deducted from refund
- See: [Shipping → Shipping Methods](topic-shipping.md#shipping-methods)

```php
    public function initiateReturn($order) {
        // Implementation
    }
```

## Refund Processing

*ReturnProcessor.php:26 — last updated 2026-03-10 01:25 by lnsy*

- Refunds issued to original payment method
- Refunds processed within 5-7 business days of receiving return
- Restocking fee of 15% for non-defective electronics
- Store credit issued immediately, no restocking fee

```php
    public function processRefund($return) {
        // Implementation
    }
```

---
*Generated on 2026-03-11 19:23:15*
