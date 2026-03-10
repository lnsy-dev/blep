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

## Refunds and Chargebacks

*PaymentGateway.php:27 — last updated 2026-03-10 01:25 by lnsy*

- Refunds appear in 5-7 business days
- Partial refunds allowed for partial returns
- Chargebacks trigger account review
- More than 2 chargebacks result in payment method ban

```php
    public function processRefund($transaction) {
        // Implementation
    }
```

---
*Generated on 2026-03-10 02:06:49*
