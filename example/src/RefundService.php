<?php

/**
 * @bl-topic Payment Processing
 * @bl-subtopic Refund Rules
 * @bl-rationale We allow full refunds within 30 days to build customer trust and reduce disputes
 * @bl-detail Full refunds available within 30 days of purchase
 * @bl-detail Partial refunds require manager approval
 * @bl-rationale Partial refunds are complex and can be abused, so we require oversight
 * @bl-detail Refunds to original payment method only
 */
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
