<?php

set_include_path(get_include_path() . ":{$CONFIG->pluginspath}userexport/phpexcel");
require_once("PHPExcel.php");
require_once("PHPExcel/IOFactory.php");
ini_set('memory_limit', '256M');

// The fields that are included to the file
$fields = get_input('fields');

// Filetype csv/excel
$filetype = get_input('filetype');

if (empty($fields)) {
	// No fields were chosen
	register_error(elgg_echo('userexport:error:nofields'));
	forward(REFERER);
}

$phpExcel = new PHPExcel();
$phpExcel->setActiveSheetIndex(0);
$phpExcel->getDefaultStyle()->getFont()->setName('Arial');

$row = 1;
$col = 0;

// Use the user field names as column headers
foreach ($fields as $title) {
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, elgg_echo($title));
    $phpExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
	$col++;
}

// Column heading are on first row so continue from the second one
$row = 2;

// Include also hidden users
$hidden_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

// Ignore access settings to get all users
elgg_set_ignore_access(true);
$batch = new ElggBatch('elgg_get_entities',	array('type' => 'user', 'limit' => false));

// Create a new row for each user
foreach ($batch as $user) {
	$col = 0;

	// Create a new cell for each piece of data
	foreach ($fields as $field) {
		if (is_array($user->$field)) {
			// Concatenate fields with multiple values into a single string
			$value = implode("|", $user->$field);
		} else {
			$value = $user->$field;
		}

		$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		$phpExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);

		$col++;
	}
	$row++;
}

// Set access level to normal
elgg_set_ignore_access(false);

// Set hidden status to normal
access_show_hidden_entities($hidden_status);

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

// Allow downloads of large files.
// See https://github.com/Elgg/Elgg/issues/1932
ob_clean();
flush();
readfile($filepath);
exit;
