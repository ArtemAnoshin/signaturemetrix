<?php

class SigmxCSV
{
    private $data;
    private $filename;
    
    public function __construct(array $data, string $file)
    {
        $this->data = $data;
        $this->filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'signature-metrix/reports/' . $file . '.csv';
    }
    
    public function add()
    {
        $buffer = fopen($this->filename, 'w');
        fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach($this->data as $line) {
            fputcsv($buffer, $line->toArray(), ';');
        }
        fclose($buffer);
    }
}