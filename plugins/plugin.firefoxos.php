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
    /* check for ant compiler */
    if (!check_exists_command('zip')) {
        echo ">zip< not found, please install!\n";
        return false;
    }
	return true;
}

function plugin_do_work($title, $input_path, $output_path) {

    /* get parameters */
    $shortopts = "";

    $longopts = array(
        "firefoxos-version-name",
        "description:"
    );
    
    $options = getopt($shortopts, $longopts);

    $description = "";
    if (isset($options['description'])) $description = $options['description'];
    $version_name = "1.0";
    if (isset($options['firefoxos-version-name'])) $version_name = $options['firefoxos-version-name'];
    
    /* create output path */
	system('mkdir '.$output_path);
	
	/* build manifest */
	$manifest = array();
	$manifest['name'] = $title;
	$manifest['description'] = $description;
	$manifest['version'] = $version_name;
	$manifest['launch_path'] = '/index.html';
	$manifest['developer'] = array();
	$manifest['developer']['name'] = AUTHOR_NAME;
	$manifest['developer']['url'] = AUTHOR_WEB;
	$manifest['default_locale'] = 'en';
	
	/* write manifest */
	$manifest_handler = fopen($output_path.'/manifest.webapp', 'w');
	fwrite($manifest_handler, json_readable_encode($manifest));
	fclose($manifest_handler);
	
	/* copy original to output path */
	system('cp -r '.$input_path.'/* '.$output_path);
	
	/* zip it up */
	system('cd '.$output_path.'; zip -r '.$title.'.zip *');
	
	return true;
}

?>
