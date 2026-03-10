<?php

/**
 * @bl-topic Returns Policy
 * @bl-subtopic Return Window
 * @bl-detail Standard return window is 30 days from delivery
 * @bl-detail Electronics have 14-day return window
 * @bl-detail Final sale items cannot be returned
 * @bl-detail Opened software/media cannot be returned unless defective
 */
class ReturnProcessor {
    
    /**
     * @bl-subtopic Return Shipping
     * @bl-detail Customer pays return shipping unless item is defective
     * @bl-detail Defective items get prepaid return label
     * @bl-detail Return shipping cost is deducted from refund
     * @bl-see Shipping: Shipping Methods
     */
    public function initiateReturn($order) {
        // Implementation
    }
    
    /**
     * @bl-subtopic Refund Processing
     * @bl-detail Refunds issued to original payment method
     * @bl-detail Refunds processed within 5-7 business days of receiving return
     * @bl-detail Restocking fee of 15% for non-defective electronics
     * @bl-detail Store credit issued immediately, no restocking fee
     */
    public function processRefund($return) {
        // Implementation
    }
}
