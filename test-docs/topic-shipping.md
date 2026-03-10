# Shipping

[← Back to Index](index.md)

## Shipping Methods

*ShippingCalculator.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Standard shipping: 5-7 business days, $5.99
- Express shipping: 2-3 business days, $12.99
- Overnight shipping: 1 business day, $24.99
- Free shipping on orders over $100

```php
class ShippingCalculator {
    
    /**
     * @bl-subtopic Address Validation
     * @bl-detail All addresses validated against USPS API
     * @bl-detail PO Boxes not allowed for express/overnight shipping
     * @bl-detail International shipping requires customs forms for orders over $800
     */
    public function validateAddress($address) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Delivery Restrictions
     * @bl-detail No Saturday delivery for standard shipping
     * @bl-detail Alaska and Hawaii have 2-day shipping delay
     * @bl-detail Some items cannot ship to certain states (e.g., lithium batteries)
     * @bl-see Returns Policy: Return Shipping
     */
    public function checkRestrictions($order) {
        // Implementation
    }
}
```

## Address Validation

*ShippingCalculator.php:15 — last updated 2026-03-10 01:25 by lnsy*

- All addresses validated against USPS API
- PO Boxes not allowed for express/overnight shipping
- International shipping requires customs forms for orders over $800

```php
    public function validateAddress($address) {
        // Implementation
    }
```

## Delivery Restrictions

*ShippingCalculator.php:25 — last updated 2026-03-10 01:25 by lnsy*

- No Saturday delivery for standard shipping
- Alaska and Hawaii have 2-day shipping delay
- Some items cannot ship to certain states (e.g., lithium batteries)
- See: [Returns Policy → Return Shipping](topic-returns-policy.md#return-shipping)

```php
    public function checkRestrictions($order) {
        // Implementation
    }
```

---
*Generated on 2026-03-10 02:06:49*
