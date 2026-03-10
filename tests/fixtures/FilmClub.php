<?php

/**
 * @bl-topic Film Club Membership
 * @bl-subtopic Eligibility
 * @bl-detail Members must be 18 or older
 * @bl-detail Members must have a valid email address
 */
class FilmClubMembership {
    
    /**
     * @bl-subtopic Subscription Tiers
     * @bl-detail Basic tier: $9.99/month, 2 films per month
     * @bl-detail Premium tier: $19.99/month, unlimited films
     * @bl-detail Premium members get early access to new releases
     */
    public function getSubscriptionTiers() {
        // Implementation
    }
    
    public function validateAge($age) {
        // @bl-detail Age verification requires government-issued ID
        if ($age < 18) {
            throw new Exception('Must be 18 or older');
        }
    }
}
