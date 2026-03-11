<?php

/**
 * @bl-topic Payment Processing
 * @bl-subtopic Accepted Payment Methods
 * @bl-detail Credit cards: Visa, Mastercard, American Express, Discover
 * @bl-detail Debit cards with Visa/Mastercard logo
 * @bl-detail PayPal and Apple Pay accepted
 * @bl-detail Gift cards can be combined with other payment methods
 */
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
