<?php

// @bl-detail This detail has no topic and should warn or go to General

/**
 * @bl-topic Edge Case Testing
 * @bl-subtopic Multiple Tags
 * @bl-detail This tests the @bl-detail tag
 * @bl-details This tests the @bl-details plural alias
 */
class EdgeCases {
    
    /**
     * @bl-subtopic Mixed DocBlock
     * @param string $value Some parameter
     * @return bool
     * @bl-detail Only @bl-* tags should be extracted, not @param or @return
     * @throws Exception
     * @bl-detail Multiple business logic details in one docblock
     */
    public function mixedTags($value) {
        // @bl-detail    Inline comment with extra whitespace
        //    @bl-detail Leading whitespace before tag
        return true;
    }
    
    /**
     * @bl-subtopic Unresolved References
     * @bl-see Nonexistent Topic
     * @bl-see Another Topic: Nonexistent Subtopic
     * @bl-detail This subtopic has cross-references that won't resolve
     */
    public function unresolvedRefs() {
        // Implementation
    }
    
    /**
     * @bl-subtopic Empty Values
     * @bl-detail
     * @bl-detail   
     * @bl-detail This is a valid detail after empty ones
     */
    public function emptyValues() {
        // Implementation
    }
}

/**
 * @bl-topic Second Topic In Same File
 * @bl-detail Multiple topics in one file should work correctly
 */
class AnotherClass {
    // Implementation
}
