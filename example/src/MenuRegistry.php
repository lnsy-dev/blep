<?php
/**
 * We set a file-global scope for the topic here
 * @bl-topic Menu Registry
 */

class MenuRegistry {

    /**
     * and then we promptly override that file-global topic for this method :shrug:
     * @bl-topic Catering Operations
     * @bl-subtopic Supplier Portal
     * @bl-detail base URL is hard coded
     * @return string URL
     */
    public static function getBaseUrl()
    {
        return 'https://menuregistry.com';
    }

    /**
     * @bl-subtopic Web Service
     * @bl-see Catering Operations
     * @return false|string menu details or failure
     */
    public static function getDetails()
    {
        // @bl-detail configuration is loaded via web service
        $config = file_get_contents(self::getBaseUrl());
        // @bl-detail configuration contains the detail endpoint, per specification
        return file_get_contents($config['detail-endpoint']);
    }

    /**
     * @bl-subtopic Web Service
     * @bl-see Catering Operations
     * @return false|string reset result or failure
     */
    public static function doReset()
    {
        // @bl-detail configuration is loaded via web service
        $config = file_get_contents(self::getBaseUrl());
        // @bl-detail configuration contains the reset endpoint, per specification
        return file_get_contents($config['reset-endpoint']);
    }

}
