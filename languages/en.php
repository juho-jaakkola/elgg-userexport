<?php

$english = array(
	'admin:userexport' => 'User export',
	'userexport:fields:select'  =>  "Select fields",
	'userexport:type:select'  =>  "File type",
	'userexport:type:csv'  =>  "csv",
	'userexport:type:excel'  =>  "xsl",
	'userexport:file:generate'  =>  "Generate",
	'admin' => 'Administrator',
	
	// Error messages
	'userexport:error:nofields' => 'Select at least one field!',
	'userexport:error:nofiledir' => 'Userexport directory is missing from dataroot. Creating a new directorty failed. Check directory permissions.',
);

add_translation('en', $english); 
