<?php
/**
 * Send the csv file to browser
 */

$filename = 'export.csv';

header("Pragma: public"); 
header("Content-type: text/csv; charset=utf-8'");
header("Content-Disposition: attachment; filename=\"$filename\"");

$filepath = elgg_get_data_path() . "userexport/{$filename}";

// Allow downloads of large files (see https://github.com/Elgg/Elgg/issues/1932)
ob_clean();
flush();
readfile($filepath);
exit;