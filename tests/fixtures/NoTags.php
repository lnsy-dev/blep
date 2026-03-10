<?php

namespace App\Services;

/**
 * Regular PHP class with no business logic tags.
 * Should be silently skipped by the parser.
 */
class NoTags {
    
    /**
     * @param string $data
     * @return array
     */
    public function process($data) {
        return json_decode($data, true);
    }
    
    // Just a normal comment
    public function anotherMethod() {
        // Nothing special here
    }
}
