<?php
/**
 * Userexport
 * 
 * Export basic user data to xsl or csv file.
 */

/**
 * Initialise the plugin.
 */
function userexport_init() {
	$actionpath = elgg_get_plugins_path() . "userexport/actions/userexport/";
	elgg_register_action("userexport/export", "$actionpath/export.php", 'admin');
	
	elgg_register_menu_item('page', array(
		'name' => 'userexport',
		'href' => 'admin/userexport',
		'text' => elgg_echo('admin:userexport'),
		'context' => 'admin',
		'section' => 'administer'
	));
}

elgg_register_event_handler('init','system','userexport_init');
