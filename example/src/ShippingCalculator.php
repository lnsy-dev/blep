<?php

/**
 * @bl-topic Shipping
 * @bl-subtopic Shipping Methods
 * @bl-detail Standard shipping: 5-7 business days, $5.99
 * @bl-detail Express shipping: 2-3 business days, $12.99
 * @bl-detail Overnight shipping: 1 business day, $24.99
 * @bl-detail Free shipping on orders over $100
 */
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
