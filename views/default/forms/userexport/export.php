<?php

$fields = array(
	'guid',
	'name',
	'username',
	'email',
	'user:language:label',
	'banned',
	'admin',
	'description',
);

$options = array();
foreach($fields as $field) {
	$options[elgg_echo($field)] = $field;
}

$form_body = '';

$form_body .= "<label>" . elgg_echo('userexport:fields:select') . "</label><br />";

$form_body .= elgg_view('input/checkboxes', array(
	'name' => 'fields',
	'options' => $options, 
));

$form_body .= "<br />";

$form_body .= "<label>" . elgg_echo('userexport:type:select') . "</label><br />";

$form_body .= elgg_view('input/dropdown', array(
	'name' => 'filetype',
	'options' => array(
		elgg_echo('userexport:type:csv') => 'csv',
		elgg_echo('userexport:type:excel') => 'excel',
	) 
));

$form_body .= "<br /><br />";

$form_body .= elgg_view('input/submit', array('value' => elgg_echo('userexport:file:generate')));

echo elgg_view('input/form', array('body' => $form_body, 'action' => 'action/userexport/export'));
