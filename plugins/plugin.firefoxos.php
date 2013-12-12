<?php

/* prevent direct access */
if (!defined('CONVERTICO_VERSION')) exit(1);

function plugin_about() {
	$ret = "Firefox OS Plugin v".plugin_get_version();
	$ret .= 'Create an Firefox OS app!';
	return $ret;
}

function plugin_get_version() {
	return "0.1";
}

function plugin_check_config() {
	return true;
}

function plugin_do_work($title, $input_path, $output_path) {
	system('mkdir '.$output_path);
	
	$manifest = array();
	$manifest['name'] = $title;
	$manifest['description'] = '';
	$manifest['launch_path'] = '/index.html';
	$manifest['developer'] = array();
	$manifest['developer']['name'] = AUTHOR_NAME;
	$manifest['developer']['url'] = AUTHOR_WEB;
	
	$manifest_handler = fopen($output_path.'/manifest.manifest', 'w');
	fwrite($manifest_handler, json_encode($manifest));
	fclose($manifest_handler);
	
	system('cp -r '.$input_path.'/* '.$output_path);
	system('cd '.$output_path.'; zip -r '.$title.'.zip *');
	
	return true;
}

?>
