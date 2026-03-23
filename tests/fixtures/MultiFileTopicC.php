<?php

/**
 * @bl-topic Shared Topic
 * @bl-subtopic From File A
 * @bl-detail Third file also contributes to From File A
 * @bl-subtopic From File C
 * @bl-detail This subtopic exists only in file C
 */
class MultiFileTopicC {

    /**
     * @bl-subtopic From File A
     * @bl-detail Method-level detail from file C contributes to From File A
     */
    public function methodC() {
        // Implementation
    }
}
