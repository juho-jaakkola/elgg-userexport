<?php
/**
 * Display a page for generating a csv file of user data
 */

elgg_require_js('userexport/generate');

$hidden_status = access_get_show_hidden_status();
access_show_hidden_entities(true);
$user_count = elgg_get_entities(array(
	'type' => 'user',
	'count' => true,
));
access_show_hidden_entities($hidden_status);

$user_counter = elgg_echo('userexport:progress', array(0, $user_count));

echo elgg_view_form('userexport/export');

$download_link = elgg_view('output/url', array(
	'href' => 'admin/userexport/download',
	'text' => elgg_echo('download'),
	'class' => 'elgg-button elgg-button-action hidden',
	'id' => 'userexport-download',
));

$restart_link = elgg_view('output/url', array(
	'href' => 'admin/userexport',
	'text' => elgg_echo('userexport:redo'),
	'class' => 'elgg-button elgg-button-action hidden',
	'id' => 'userexport-redo',
));

echo <<<HTML
	<span id="userexport-data" data-offset="0" data-total="$user_count"></span>
	<p id="userexport-user-counter" class="userexport-hidden">$user_counter</p>
	<div class="elgg-progressbar userexport-hidden mvl" id ="elgg-progressbar-userexport"></div>
	$download_link
	$restart_link
HTML;
