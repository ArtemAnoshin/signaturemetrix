<?php

class SigmxSignatureResult
{
    public string $id;
    public string $body;
    public string $working_time;
    
    public function __construct(
        $id,
        $body,
        $working_time
    )
    {
        $this->id = $id;
        $this->body = $body;
        $this->working_time = $working_time;
    }
}