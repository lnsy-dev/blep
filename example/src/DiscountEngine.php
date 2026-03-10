<?php

/**
 * @bl-topic Discount Rules
 * @bl-subtopic Coupon Codes
 * @bl-detail Coupon codes are case-insensitive
 * @bl-detail Only one coupon code per order
 * @bl-detail Coupons cannot be combined with sale items
 * @bl-detail Expired coupons are rejected at checkout
 */
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
