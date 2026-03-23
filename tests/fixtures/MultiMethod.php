<?php

/**
 * @bl-topic Payment Processing
 * @bl-subtopic Validation
 */
class MultiMethod {

    /**
     * @bl-subtopic Validation
     * @bl-detail Credit card numbers must pass Luhn check
     */
    public function validateCard($number) {
        // Luhn algorithm implementation
        return true;
    }

    /**
     * @bl-subtopic Validation
     * @bl-detail CVV must be exactly 3 digits for Visa/Mastercard, 4 for Amex
     */
    public function validateCvv($cvv, $cardType) {
        $length = $cardType === 'amex' ? 4 : 3;
        return strlen($cvv) === $length;
    }

    /**
     * @bl-subtopic Refunds
     * @bl-detail Refunds are only allowed within 30 days of the original charge
     * @bl-detail Partial refunds are not supported for subscription payments
     */
    public function processRefund($chargeId, $amount) {
        // Implementation
    }
}
