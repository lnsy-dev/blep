<?php

/**
 * @bl-topic Order Processing
 * @bl-subtopic Validation Rules
 * @bl-detail Orders over $1000 require manager approval
 * @bl-detail Minimum order value is $10
 * @bl-detail Maximum 50 items per order
 */
class OrderService
{
    public function createOrder(array $data): Order
    {
        // @bl-detail Inventory is reserved for 15 minutes during checkout
        $this->reserveInventory($data['items']);
        
        /**
         * @bl-subtopic Payment Processing
         * @bl-detail Credit card payments are processed immediately
         * @bl-detail PayPal payments may take up to 24 hours to confirm
         * @bl-see Payment Gateway Integration
         */
        $payment = $this->processPayment($data['payment']);
        
        return new Order($data, $payment);
    }
    
    /**
     * @bl-subtopic Shipping Rules
     * @bl-detail Free shipping on orders over $50
     * @bl-detail Express shipping available for orders under 10 lbs
     * @bl-detail International orders require customs declaration
     */
    public function calculateShipping(Order $order): float
    {
        // Implementation
    }
}

/**
 * @bl-topic User Authentication
 * @bl-subtopic Password Requirements
 * @bl-detail Password must be at least 12 characters
 * @bl-detail Password must contain uppercase, lowercase, number, and symbol
 * @bl-detail Password cannot contain username or email
 * @bl-detail Password expires after 90 days
 */
class AuthService
{
    /**
     * @bl-subtopic Login Attempts
     * @bl-detail Account locked after 5 failed login attempts
     * @bl-detail Lockout duration is 30 minutes
     * @bl-detail Admin accounts have stricter lockout (3 attempts)
     * @bl-see Security Monitoring
     */
    public function login(string $username, string $password): bool
    {
        // Implementation
    }
}

/**
 * @bl-topic Payment Gateway Integration
 * @bl-subtopic Transaction Limits
 * @bl-detail Maximum transaction amount is $10,000
 * @bl-detail Transactions over $5,000 require additional verification
 * @bl-detail Failed transactions are retried up to 3 times
 */
class PaymentGateway
{
    // Implementation
}
