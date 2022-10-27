<?php

class SigmxFileResult
{
    public string $path;
    public int $size;
    public string $status;
    public array $signatures_found;
    public int $signatures_check_time;
    public string $error;
    
    public function __construct(
        $path,
        $size,
        $status,
        $signatures_found,
        $signatures_check_time,
        $error
    )
    {
        $this->path = $path;
        $this->size = $size;
        $this->status = $status;
        $this->signatures_found = $signatures_found;
        $this->signatures_check_time = $signatures_check_time;
        $this->error = $error;
    }
    
    public function updateSignaturesFound($id)
    {
        $this->signatures_found[] = $id;
    }
    
    public function setCheckingTime($start, $end)
    {
        $this->signatures_check_time = floor($end * 1000) - floor($start * 1000);
    }
    
    public function toArray(): array
    {
        return [
            $this->path,
            (string)$this->size,
            $this->status,
            implode(',', $this->signatures_found),
            (string)$this->signatures_check_time,
            $this->error            
        ];
    }
}