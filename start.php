<?php
/**
 * Userexport
 * 
 * Export basic user data to xsl or csv file.
 */

elgg_register_event_handler('init','system','userexport_init');

/**
 * Initialise the plugin.
 */
function userexport_init() {
	$actionpath = elgg_get_plugins_path() . "userexport/actions/userexport/";
	elgg_register_action("userexport/export", "$actionpath/export.php", 'admin');

	elgg_extend_view('css/admin', 'userexport/css');

	$userexport_js = elgg_get_simplecache_url('js', 'userexport/generate');
	elgg_register_simplecache_view('js/userexport/generate');
	elgg_register_js('elgg.userexport', $userexport_js);

	elgg_register_menu_item('page', array(
		'name' => 'userexport',
		'href' => 'admin/userexport',
		'text' => elgg_echo('admin:userexport'),
		'context' => 'admin',
		'section' => 'administer',
		'parent_name' => 'users',
	));
}