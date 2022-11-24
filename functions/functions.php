<?php

/**
 * Stage: 2
 * Checking path
 */
function sigmx__path_validation($path) : bool
{
    $root = WP_ROOT_PATH . DIRECTORY_SEPARATOR;
    $path = trim($path, '/');
    
    if (is_dir($root . $path) || file_exists($root . $path)) {
        return true;
    }
    
    return false;
}

/**
 * Get all files from directory and subdirectory
 *
 * @param $dir
 *
 * @return array
 */
function sigmx__get_files($dir) : array
{
    $root = array_diff(scandir($dir), array('.', '..'));

    foreach($root as $value) {
        if (is_dir("$dir/$value")) {
            foreach(sigmx__get_files("$dir/$value") as $files)
            {
                $result[] = $files;
            }
        } elseif (is_file("$dir/$value") && pathinfo($value, PATHINFO_EXTENSION) === 'php') {
            $result[] = "$dir/$value";
        }
    }

    return !empty($result) ? $result : array();
}

/**
 * Returns true if $signature is regexp, else return false. Supports modifications set [imSsxADUuXJ].
 *
 * @param string $signature - signature expression from DB
 * @param string $delimiters - delimiters for regexp. Default set is '#/'. Do not use @ symbol as delimiter.
 *
 * @return bool
 */
function sigmx__is_regexp( string $signature, string $delimiters = '#/') : bool
{
    $pattern_modifiers = '[imSsxADUuXJ]{0,11}';
    $limit             = strlen($delimiters) - 1;
    for ( $i = 0; $i <= $limit; $i++ ) {
        $pattern = '@^' . $delimiters[$i] . '.*' . $delimiters[$i] . $pattern_modifiers . '$@';
        if ( preg_match($pattern, $signature) ) {
            return true;
        }
    }

    return false;
}
