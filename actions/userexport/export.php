<?php
/**
 * Generate new rows to a csv file starting from $limit
 */

$offset = get_input('offset');
$fields = get_input('fields');

if (empty($fields)) {
	// No fields were chosen
	register_error(elgg_echo('userexport:error:nofields'));
	forward(REFERER);
}

$exporter = new UserExport();
$exporter->setFields($fields);
$exporter->setOffset($offset);
$exporter->generate();

$new_offset = $exporter->getNumberOfAddedRows() + $offset;

$result = new stdClass();
$result->offset = $new_offset;

echo json_encode($result);
exit;