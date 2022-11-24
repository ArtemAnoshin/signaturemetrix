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
        require_once 'SigmxFileResult.php';
        //require_once 'SigmxCSV.php';
        require_once 'SigmxJSON.php';
        require_once 'SigmxSignaturesResultRepository.php';
        $root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        $path = trim($path, '/');
        $path = $root . $path;
        
        global $wpdb;

        $this->path = $path;
        $this->db = $wpdb;
        $this->signatures = $this->db->get_results("SELECT * FROM {$wpdb->base_prefix}spbc_scan_signatures WHERE type='CODE_PHP'");
        if (is_dir($this->path)) {
            $this->files = sigmx__get_files($this->path);
            $this->saveCountFiles(count($this->files));
        }
    }
    
    public function getResult()
    {
        if ($this->files) {
            $report_files_file_name = 'files-working-time-' . date('YmdHis');
            $report_signatures_file_name = 'signatures-working-time-' . date('YmdHis');
            $count_files = $this->getCountFiles();
            $checking_results = [];
            SigmxSignaturesResultRepository::clearAllSignatureResult();
            
            foreach ($this->files as $file) {
                $checking_results[] = $this->scanFile($file);
            }

            // Save result to csv
            //$csv = new SigmxCSV($checking_results, $report_file_name);
            //$csv->add();

            // Save files results to JSON
            $files_to_json = new SigmxJSON($checking_results, $count_files, $report_files_file_name);
            $files_to_json->add();

            // Save signatures results to JSON
            $all_signature_result = SigmxSignaturesResultRepository::getAllSignatureResult();
            $signatures_json = new SigmxJSON($all_signature_result, $count_files, $report_signatures_file_name);
            $signatures_json->add();

            return $checking_results;
        }
        
        return false;
    }
    
    public function scanFile($file): array
    {
        $file_result = new SigmxFileResult(
            $file,
            0,
            'OK',
            array(),
            0,
            ''
        );

        if (!file_exists($file)) {
            $file_result->status = 'FILE_NOT_EXISTS';
        }

        if (!is_readable($file)) {
            $file_result->status = 'FILE_NOT_READABLE';
        }

        $filesize = filesize($file);

        if ($filesize > $this::FILE_MAX_SIZE || !$filesize) {
            $file_result->status = 'FILE_SIZE_NOT_VALID';
        }
        
        $file_result->size = $filesize;

        $file_content = file_get_contents($file);
    
        $all_signature_result = SigmxSignaturesResultRepository::getAllSignatureResult();

        // start time
        $checking_time_start = microtime(true);

        foreach ($this->signatures as $signature) {
            $is_regexp = sigmx__is_regexp($signature->body);
    
            $signature_time_start = microtime(true);
            
            if ($is_regexp && preg_match($signature->body, $file_content)) {
                // signature found by regexp
                $file_result->updateSignaturesFound($signature->name);
            } elseif (
                ! $is_regexp &&
                (
                    strripos($file_content, stripslashes($signature->body)) !== false ||
                    strripos($file_content, $signature->body) !== false
                )
            ) {
                // signature found by string
                $file_result->updateSignaturesFound($signature->name);
            }
    
            $signature_time_end = microtime(true);
            $signature_working_time = $signature_time_end - $signature_time_start;
            $prev_working_time = isset($all_signature_result[$signature->name]['working_time']) ? 
                $all_signature_result[$signature->name]['working_time'] + $signature_working_time :
                $signature_working_time;

            $all_signature_result[$signature->name] = [
                'working_time' => round($prev_working_time, 4)
            ];
        }

        //end time
        $checking_time_end = microtime(true);
        $file_result->setCheckingTime($checking_time_start, $checking_time_end);
        
        SigmxSignaturesResultRepository::setAllSignatureResult($all_signature_result);

        return $file_result->toArray();
    }
    
    public static function getAllSignatureResult()
    {
        require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
        require_once 'SigmxSignaturesResultRepository.php';

        return SigmxSignaturesResultRepository::getAllSignatureResult();
    }

    private function saveCountFiles(int $count) {
        update_option('sigmx__count_files', $count);
    }

    private function getCountFiles() {
        return get_option('sigmx__count_files');
    }
}
