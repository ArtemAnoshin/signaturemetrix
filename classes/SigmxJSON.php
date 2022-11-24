<?php

class SigmxJSON {
    private array $data;
    private $count_files;
    private string $filename;

    /**
     * @param array $checking_results
     * @param false|mixed|void $count_files
     * @param string $report_file_name
     */
    public function __construct( array $checking_results, $count_files, string $report_file_name )
    {
        $this->data = $checking_results;
        $this->count_files = $count_files;
        $this->filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'signature-metrix/reports/' . $report_file_name . '.json';
    }

    public function add()
    {
        $data = [
            'count_files' => $this->count_files,
            'results' => $this->data
        ];

        file_put_contents($this->filename, json_encode($data));
    }
}