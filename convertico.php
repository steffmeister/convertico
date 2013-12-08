#!/usr/bin/php
<?php

define('CONVERTICO_VERSION', '0.1');
define('OUTPUT_DIR', 'output/');
define('PLUGIN_DIR', 'plugins/');

define('PLUGIN_ANDROID_SDK_PATH', '/home/steff/Entwicklung/android-sdk-linux_86/');

echo 'convertico '.CONVERTICO_VERSION."\n*******************\n";

$shortopts = "";

$longopts = array(
	"help",
	"input:",
	"title:",
	"plugin:",
	"list-plugins"
);

$options = getopt($shortopts, $longopts);
//print_r($options);


if (isset($options['help'])) {
	echo "Available parameters:\n";
	foreach($longopts as $opt) {
		echo $opt."\n";
	}
	exit(0);
}

if (isset($options['list-plugins'])) {
	echo "Available plugins:\n";
	$plugins = get_available_plugins();
	if ($plugins != false) {
		if (count($plugins) > 0) {
			foreach($plugins as $plugin) {
				echo "- ".$plugin."\n";
			}
		} else {
			echo "No plugins found, sorry!\n";
		}
	} else {
		echo "Please check plugin directory! (Existence, Permissions, ...)\n";
	}
	exit(0);
}


if ((isset($options['plugin'])) && (isset($options['input']))) {
	$target_plugin = '';
	$plugins = get_available_plugins();
	if (count($plugins) > 0) {
		/* check for plugins */
		foreach($plugins as $plugin) {
			if ($options['plugin'] == $plugin) $target_plugin = $plugin;
		}
		/* specified plugin not in list */
		if ($target_plugin == '') {
			echo "Specified plugin not found!\n";
			exit(0);
		}
		/* check if input is a valid directory */
		if (!is_dir($options['input'])) {
			echo ">input< is not a valid directory.\n!";
		}
		echo "Loading plugin ".PLUGIN_DIR.'plugin.'.$target_plugin.'.php...';
		require PLUGIN_DIR.'plugin.'.$target_plugin.'.php';
		echo "done\n";
		echo "Plugin version is... ".plugin_get_version()."\n";
		if (plugin_check_config() == false) {
			echo "Misconfiguration. Please check.\n";
			exit(1);
		}
		
		$title = basename($options['input']);
		if (isset($options['title'])) {
			$title = $options['title'];
		}
		
		if (!plugin_do_work($title, $options['input'], OUTPUT_DIR.$title.'-'.$plugin)) {
			echo "Error occurred...\n";
			exit (1);
		}
		
		echo "I think we're done here.\n";
		
	} else {
		echo "No plugins found, sorry!\n";
		exit(0);
	}
}




function get_available_plugins() {
	$plugins = array();
	if (is_dir(PLUGIN_DIR)) {
		if ($dh = opendir(PLUGIN_DIR)){
			while (($file = readdir($dh)) !== false){
				if ((substr($file, -4) == '.php') && (substr($file, 0, strlen('plugin.')) == 'plugin.')) {
					// plugin.android.php > android
					$plugins[] = substr($file, strlen('plugin.'), strpos($file, '.php')-strlen($file));
				}
			}
			closedir($dh);
			return $plugins;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

?>
