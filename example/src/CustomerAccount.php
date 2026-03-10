<?php

/**
 * @bl-topic Customer Accounts
 * @bl-subtopic Account Creation
 * @bl-detail Email must be unique across all accounts
 * @bl-detail Password must be at least 12 characters
 * @bl-detail Password must contain uppercase, lowercase, number, and symbol
 * @bl-detail Disposable email domains are blocked
 */
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
