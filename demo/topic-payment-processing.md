# Payment Processing

[← Back to Index](index.md)

## Accepted Payment Methods

*PaymentGateway.php:6 — last updated 2026-03-10 01:25 by lnsy*

- Credit cards: Visa, Mastercard, American Express, Discover
- Debit cards with Visa/Mastercard logo
- PayPal and Apple Pay accepted
- Gift cards can be combined with other payment methods

```php
class PaymentGateway {
    
    /**
     * @bl-subtopic Payment Security
     * @bl-detail All transactions use PCI-DSS compliant processing
     * @bl-detail CVV required for all card transactions
     * @bl-detail 3D Secure verification for international cards
     * @bl-detail Failed payment attempts are logged for fraud detection
     */
    public function processPayment($order, $paymentMethod) {
        // @bl-detail Card authorization holds funds for 7 days
        // @bl-detail Capture happens when order ships
    }
    
    /**
     * @bl-topic Order Processing
     * @bl-subtopic Payment Requirements
     * @bl-detail Payment must be authorized before order enters processing
     * @bl-detail Orders with declined payments remain in "pending" for 24 hours
     * @bl-rationale Gives customers time to update payment method without losing cart
     * @bl-detail After 24 hours, unpaid orders are automatically cancelled
     */
    public function validateOrderPayment($order) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Refunds and Chargebacks
     * @bl-detail Refunds appear in 5-7 business days
     * @bl-detail Partial refunds allowed for partial returns
     * @bl-detail Chargebacks trigger account review
     * @bl-detail More than 2 chargebacks result in payment method ban
     */
    public function processRefund($transaction) {
        // Implementation
    }
}
```

## Payment Security

*PaymentGateway.php:15 — last updated 2026-03-10 01:25 by lnsy*

- All transactions use PCI-DSS compliant processing
- CVV required for all card transactions
- 3D Secure verification for international cards
- Failed payment attempts are logged for fraud detection
- Card authorization holds funds for 7 days
- Capture happens when order ships

```php
    public function processPayment($order, $paymentMethod) {
        // @bl-detail Card authorization holds funds for 7 days
        // @bl-detail Capture happens when order ships
    }
```

## Refund Rules

*RefundService.php:7 — last updated 2026-03-10 02:29 by lnsy*

- Full refunds available within 30 days of purchase
- Partial refunds require manager approval
- Refunds to original payment method only

```php
class RefundService {
    
    /**
     * @bl-subtopic Fraud Prevention
     * @bl-rationale Multiple refund requests from the same customer often indicate fraud or abuse
     * @bl-detail More than 3 refunds in 6 months triggers account review
     * @bl-detail Refunds over $500 require identity verification
     */
    public function processRefund($order) {
        // Implementation
    }
}
```

## Fraud Prevention

*RefundService.php:17 — last updated 2026-03-10 02:29 by lnsy*

- More than 3 refunds in 6 months triggers account review
- Refunds over $500 require identity verification

```php
    public function processRefund($order) {
        // Implementation
    }
```

---
*Generated on 2026-03-11 21:03:03*
