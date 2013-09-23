<?php

$fields = array(
	'guid',
	'name',
	'username',
	'email',
	'language',
	'banned',
	'admin',
	'description',
	'interests',
	'days_of_last_login',
);

// Allow plugins to add new fields
$fields = elgg_trigger_plugin_hook('fields', 'userexport', array(), $fields);

$options = array();
foreach($fields as $field) {
	$fieldname = elgg_echo($field);
	if ($field == $fieldname) {
		// No translation was found. Try a different one.
		$fieldname = elgg_echo("profile:$field");
	}

	$options[$fieldname] = $field;
}

$fields_label = elgg_echo('userexport:fields:select');
$fields_input = elgg_view('input/checkboxes', array(
	'name' => 'fields',
	'options' => $options, 
));
$offset_input = elgg_view('input/hidden', array(
	'name' => 'offset',
	'value' => 0,
));
$submit_input = elgg_view('input/submit', array(
	'value' => elgg_echo('userexport:file:generate')
));

echo <<<FORM
	<div>
		<label>$fields_label</label>
		$fields_input
	</div>
	<div class="elgg-foot">
		$offset_input
		$submit_input
	</div>
FORM;
