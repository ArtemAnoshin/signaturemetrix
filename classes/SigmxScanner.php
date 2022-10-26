<?php

class SigmxScanner
{
    /**
     * @var string 
     */
    private $path;
    
    /**
     * @var object
     */
    private $db;
    
    /**
     * @var array
     */
    private $signatures;
    
    /**
     * @var array
     */
    private $files;
    
    /**
     * Max file size for scanner
     */
    const FILE_MAX_SIZE = 524288;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        define('SHORTINIT', true);
        require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
        $root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        $path = trim($path, '/');
        $path = $root . $path;
        
        global $wpdb;

        $this->path = $path;
        $this->db = $wpdb;
        $this->signatures = $this->db->get_results("SELECT * FROM {$wpdb->base_prefix}spbc_scan_signatures WHERE type='CODE_PHP'");
        if (is_dir($this->path)) {
            $this->files = sigmx__get_files($this->path);
        }
    }
    
    public function getResult(): bool
    {
        if ($this->files) {
            foreach ($this->files as $file) {
                $result = $this->scanFile($file);
        
//                $result['status']     = isset($result['status']) ? $result['status'] : 'UNKNOWN';
//                $result['severity']   = isset($result['severity']) ? $result['severity'] : 'NULL';
//                $result['weak_spots'] = ! empty($result['weak_spots']) ? json_encode(
//                    $result['weak_spots']
//                ) : 'NULL';
//        
//                $processed_items[$file['fast_hash']]['status'] = ! empty($file['status']) && $file['status'] === 'MODIFIED'
//                    ? 'MODIFIED'
//                    : $result['status'];
//        
//                $status     = ! empty($file['status']) && $file['status'] === 'MODIFIED' ? 'MODIFIED' : $result['status'];
//                $weak_spots = $result['weak_spots'];
//                $severity   = ! empty($file['severity']) ? $file['severity'] : $result['severity'];
//        
//                $result_db = $this->db->execute(
//                    'UPDATE ' . SPBC_TBL_SCAN_FILES
//                    . ' SET'
//                    . ' checked_signatures = 1,'
//                    . ' status =   \'' . $status . '\','
//                    . ' severity = ' . Helper::prepareParamForSQLQuery($severity) . ','
//                    . ' weak_spots = ' . Helper::prepareParamForSQLQuery($weak_spots)
//                    . ' WHERE fast_hash = \'' . $file['fast_hash'] . '\';'
//                );
//        
//                // Added scan result to table with log
//                // TODO: refactor this, create one SQL for insert all files instead insert by one
//                $scan_results_log_repository = new ScanResultsLogRepository();
//                $scan_results_log_repository->addScanResultsLogRow($file['fast_hash'], 'SIGNATURE', $status);
//        
//                $result_db !== null ? $scanned++ : $scanned;
            }
        }
        
        return false;
    }
    
    public function scanFile($file) : array
    {    
        if (!file_exists($file)) {
            return [
                'status' => 'FILE_NOT_EXISTS'
            ];
        }
        
        if (!is_readable($file)) {
            return [
                'status' => 'FILE_NOT_READABLE'
            ];
        }
    
        $filesize = filesize($file);
        if ($filesize > $this::FILE_MAX_SIZE || !$filesize) {
            return [
                'size' => $filesize,
                'status' => 'FILE_SIZE_NOT_VALID'
            ];
        }

        $file_content = file_get_contents($file);

        foreach ($this->signatures as $signature) {
            $is_regexp = sigmx__is_regexp($signature['body']);

            if ($is_regexp && preg_match($signature['body'], $file_content)) {
                // signature found by regexp
                
            } elseif (
                ! $is_regexp &&
                (
                    strripos($file_content, stripslashes($signature['body'])) !== false ||
                    strripos($file_content, $signature['body']) !== false
                )
            ) {
                // signature found by string
                
            } else {
                // signature not found
            }
        }
        
        return array();
    }
}
