<?php

class SigmxSignaturesResultRepository
{
    protected static SigmxSignaturesResultRepository $_instance;
    
    private function __construct() {
    }
    
    public static function getInstance(): SigmxSignaturesResultRepository {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
        
    private function __clone() {
    }
    
    public function __wakeup() {
    }
    
    public static function getAllSignatureResult()
    {
        return get_option('sigmx__all_signatures', []);
    }
    
    public static function setAllSignatureResult($all_signature_result)
    {
        update_option('sigmx__all_signatures', $all_signature_result);
    }
    
    public static function clearAllSignatureResult()
    {
        delete_option('sigmx__all_signatures');
    }
}