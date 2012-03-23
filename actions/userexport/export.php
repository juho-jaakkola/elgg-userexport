<?php

admin_gatekeeper();

elgg_set_context('admin');

set_include_path(get_include_path() . ":{$CONFIG->pluginspath}userexport/phpexcel");
require_once("PHPExcel.php");
require_once("PHPExcel/IOFactory.php");
ini_set('memory_limit', '256M');

// Use the field names as header
$header = get_input('fields');
$filetype = get_input('filetype');

if (empty($header)) {
	// No fields were chosen
	register_error(elgg_echo('userexport:error:nofields'));
	forward(REFERER);
}

// Ignore access settings to get all users
elgg_set_ignore_access(true);

// Get all users
$users = elgg_get_entities(array('type' => 'user', 'limit' => false));

$exportData = array();
// Get requested data from users
if ($users) {
	foreach($users as $user) {
		// Arrange user metadata to an array
		foreach ($header as $field) {
			if ($field === 'is_admin') {
				$userRow[$field] = $user->isAdmin();
				continue;
			}
			
			if (is_array($user->$field)) {
				$userRow[$field] = implode("|", $user->$field);
			} else {
				$userRow[$field] = $user->$field;
			}
		}
		$exportData[$user->guid] = $userRow;
	}
}

// Set access level to normal
elgg_set_ignore_access(false);

$phpExcel = new PHPExcel();
$phpExcel->setActiveSheetIndex(0);
$phpExcel->getDefaultStyle()->getFont()->setName('Arial');

$row = 1;
$col = 0;
// Set column headings to first row
foreach ( $header as $title ) {
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, elgg_echo($title));
    $phpExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
	$col++;
}

$row = 2;
// Create own row for eacg user
foreach ( $exportData as $data ) {
    $col = 0;
	// Set the values for the row
    foreach ( $data as $column ) {
        $phpExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $column);
		$phpExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
        $col++;
    }
	$row++;
}

// Find out the type of the export file
if ($filetype == 'excel') {
	$writerType = 'Excel5';
	$fileSuffix = 'xls';
} elseif ($filetype == 'csv') {
	$writerType = 'CSV';
	$fileSuffix = 'csv';
}

// Define file name and location
$filename = "export.{$fileSuffix}";
$filedir  = "{$CONFIG->dataroot}userexport";
$filepath = "{$filedir}/{$filename}";

// Check that file directory exists
if (!is_dir($filedir)) {
	// Create the userexport data directory
	if (!mkdir($filedir, 0700)) {
		// Creating the directory failed
		register_error(elgg_echo('userexport:error:nofiledir'));
		forward(REFERER);
	}
}

// Create writer and write the file to data directory
$phpExcelWriter = PHPExcel_IOFactory::createWriter($phpExcel, $writerType);
$phpExcelWriter->save($filepath);

// Send the file to browser
header("Pragma: public"); 
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");

// allow downloads of large files.
// see http://trac.elgg.org/ticket/1932
ob_clean();
flush();
readfile($filepath);
exit;

