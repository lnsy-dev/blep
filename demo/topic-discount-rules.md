# Discount Rules

[← Back to Index](index.md)

## Coupon Codes

*DiscountEngine.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Coupon codes are case-insensitive
- Only one coupon code per order
- Coupons cannot be combined with sale items
- Expired coupons are rejected at checkout

```php
class DiscountEngine {
    
    /**
     * @bl-subtopic Automatic Discounts
     * @bl-detail Orders over $100 get free shipping
     * @bl-detail Buy 3 or more of same item, get 10% off those items
     * @bl-detail First-time customers get 15% off their first order
     * @bl-detail Automatic discounts stack with coupon codes
     */
    public function calculateDiscount($order) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Loyalty Program
     * @bl-detail Customers earn 1 point per dollar spent
     * @bl-detail 100 points = $5 discount
     * @bl-detail Points expire after 12 months of inactivity
     * @bl-detail Points cannot be redeemed on gift cards
     * @bl-see Customer Accounts: Loyalty Tier Benefits
     */
    public function applyLoyaltyPoints($customer, $order) {
        // Implementation
    }
}
```

## Automatic Discounts

*DiscountEngine.php:15 — last updated 2026-03-10 01:25 by lnsy*

- Orders over $100 get free shipping
- Buy 3 or more of same item, get 10% off those items
- First-time customers get 15% off their first order
- Automatic discounts stack with coupon codes

```php
    public function calculateDiscount($order) {
        // Implementation
    }
```

## Loyalty Program

*DiscountEngine.php:26 — last updated 2026-03-10 01:25 by lnsy*

- Customers earn 1 point per dollar spent
- 100 points = $5 discount
- Points expire after 12 months of inactivity
- Points cannot be redeemed on gift cards
- See: [Customer Accounts → Loyalty Tier Benefits](topic-customer-accounts.md#loyalty-tier-benefits)

```php
    public function applyLoyaltyPoints($customer, $order) {
        // Implementation
    }
```

---
*Generated on 2026-03-11 21:03:03*
