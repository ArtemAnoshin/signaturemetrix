<?php

require_once 'functions/functions.php';
require_once 'classes/SigmxScanner.php';

$path_empty = false;
$path_valid = true;
$scan_result = false;

/**
 * Stage: 1
 * Get file/directory path
 */
if (empty($_POST['path']) && isset($_POST['submit'])) {
    $path_empty = true;
}

/**
 * Stage: 2
 * Checking path
 */
if (!empty($_POST['path'])) {
    $path_valid = sigmx__path_validation($_POST['path']);
}

/**
 * Stage: 3
 * Start signature scanner
 */
if ($path_valid && isset($_POST['submit'])) {
    $sigmx_scanner = new SigmxScanner($_POST['path']);
    $scan_result = $sigmx_scanner->getResult();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Sugnature Metrix</title>
</head>
<body>
    <div class="container">
        <h1 class="mb-5">Signature Metrix</h1>
        <form class="row g-3 mb-5" action="/signature-metrix/" method="post">
            <div class="col-md-6">
                <label for="path" class="form-label">File/Directory Path</label>
                <input type="text" class="form-control" id="path" name="path" placeholder="/dir/file.php">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit" value="1">Start</button>
            </div>
        </form>
        
        <?php 
            if ($path_empty) {
                ?>
                <div class="alert alert-primary" role="alert">
                    Path empty, fill the path
                </div>
                <?php
            }

            if (!$path_valid) {
                ?>
                <div class="alert alert-primary" role="alert">
                    File or Directory not exists
                </div>
                <?php
            }

            if ($scan_result) {
                print_r($scan_result);
            }
        ?>
    </div>
</body>
</html>