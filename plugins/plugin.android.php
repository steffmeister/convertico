<?php

/* prevent direct access */
//if (!defined(CONVERTICO_VERSION)) exit(1);

function plugin_about() {
	$ret = "Android Plugin v".plugin_get_version();
	$ret .= 'Create an Android app!';
	return $ret;
}

function plugin_get_version() {
	return "0.1";
}

function plugin_check_config() {
	/* check for android sdk */
	if (!file_exists(PLUGIN_ANDROID_SDK_PATH.'tools/android')) {
		echo "ERROR: please check your PLUGIN_ANDROID_SDK_PATH. It has to point to a valid Android SDK!\n";
		return false;
	}
	/* everything is fine */
	return true;
}

function plugin_do_work($title, $input_path, $output_path) {
	if (!is_dir($input_path)) {
		echo "Invalid input path :(\n";
		return false;
	}
	if (is_dir($output_path)) {
		echo "Output directory already exists!\n";
		return false;
	}
	
	echo "Generating project...";
	$package_name = 'at.convertico.'.str_replace('.', '', strtolower($title));
	system(PLUGIN_ANDROID_SDK_PATH.'tools/android create project --target 19 --name '.$title.' --path '.$output_path.' --activity MainActivity --package '.$package_name, $return_value);
	//echo $return_value."\n";
	
	if ($return_value != 0) {
		echo "Looks like an error occurred, sorry :(\n";
	}
	
	echo "Generating layout xml...\n";
	system('cp '.PLUGIN_DIR.'android/main.xml '.$output_path.'/res/layout/');
	
	echo "Generating MainActivity source...\n";
	$source_file = $output_path.'/src/'.str_replace('.', '/', $package_name).'/MainActivity.java';
	system('echo package '.$package_name.'\; > '.$source_file);
	system('cat '.PLUGIN_DIR.'android/MainActivity.java >> '.$source_file);
	
	echo "Converting strings.xml...\n";
	system('cd '.$output_path.'/res/values/; cat strings.xml | sed -r \'s/MainActivity/'.$title.'/g\' > strings-new.xml; rm strings.xml; mv strings-new.xml strings.xml');
	
	echo "Copying assets...\n";
	system('mkdir '.$output_path.'/assets');
	system('cp -r '.$input_path.'/* '.$output_path.'/assets');
	
	
	echo "Building project...\n";
	system('cd '.$output_path.'; ant debug;');
	
	return true;
}

?>
