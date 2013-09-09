<?php

$english = array(
	'admin:userexport' => 'User export',
	'userexport:fields:select'  =>  "Select fields",
	'userexport:file:generate'  =>  "Generate",
	'userexport:progress' => 'Processed %s/%s users', 
	'userexport:redo' => 'Generate again',
	'is_admin' => 'Administrator',
	'guid' => 'GUID',
	'download' => 'Download',
	'profile:language' => 'Language',

	// Error messages
	'userexport:error:nofields' => 'Select at least one field!',
	'userexport:error:nofiledir' => 'Userexport directory is missing from dataroot. Creating a new directorty failed. Check directory permissions.',
);

add_translation('en', $english);