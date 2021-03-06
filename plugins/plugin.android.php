<?php

/* prevent direct access */
if (!defined('CONVERTICO_VERSION')) exit(1);

define('RESOLUTION_LDPI', '36x36');
define('RESOLUTION_MDPI', '48x48');
define('RESOLUTION_HDPI', '72x72');
define('RESOLUTION_XHDPI', '96x96');

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
		
	/* check for ant compiler */
    if (!check_exists_command('ant')) {
        echo ">ant< not found, please install!\n";
        return false;
    }
	
	/* check for "convert" of imagemagick */
    if (!check_exists_command('convert')) {
        echo ">convert< not found, resizing images will not be possible...\n";
    }
	
	
	/* everything is fine */
	return true;
}

function plugin_do_work($title, $input_path, $output_path) {

	$longopts = array(
		"android-package-name:",
		"android-target-sdk:",
		"android-min-sdk:",
		"android-version-code:",
		"android-version-name:",
		"android-manifest:",
		"android-launcher-image:",
		"android-internet-permission",
		"android-activity-name:",
		"android-ant-release"
	);

	$options = parseParameters($longopts);

	//print_r($options);

	// set some defaults and interprete command line params
	$package_name = 'at.convertico.'.str_replace('.', '', strtolower($title));
	if (isset($options['android-package-name'])) $package_name = $options['android-package-name'];
	$version_code = 1;
	if (isset($options['android-version-code'])) $version_code = $options['android-version-code'];
	$version_name = "1.0";
	if (isset($options['android-version-name'])) $version_name = $options['android-version-name'];
	$target_sdk = "19";
	if (isset($options['android-target-sdk'])) $target_sdk = $options['android-target-sdk'];
	$min_sdk = $target_sdk;
	if (isset($options['android-min-sdk'])) $min_sdk = $options['android-min-sdk'];
	$activity_name = 'MainActivity';
	if (isset($options['android-activity-name'])) $activity_name = $options['android-activity-name'];
	$manifest_file = PLUGIN_DIR.'android/AndroidManifest.xml';
	if (isset($options['android-manifest'])) $manifest_file = $options['android-manifest'];
	$android_launcher_image = '';
	if (isset($options['android-launcher-image'])) $android_launcher_image = $options['android-launcher-image'];
	$ant_mode = "debug";
	if (isset($options['android-ant-release'])) $ant_mode = "release";
	
	
	echo "Generating project...";
	system(PLUGIN_ANDROID_SDK_PATH.'tools/android create project --target android-'.$target_sdk.' --name '.$title.' --path '.$output_path.' --activity '.$activity_name.' --package '.$package_name, $return_value);
	//echo $return_value."\n";
	
	if ($return_value != 0) {
		echo "Looks like an error occurred, sorry :( (Return value was ".$return_value.")\n";
		return false;
	}
	
	echo "Generating layout xml...\n";
	system('cp '.PLUGIN_DIR.'android/main.xml '.$output_path.'/res/layout/');
	
	echo "Generating '.$activity_name.' source...\n";
	$source_file = $output_path.'/src/'.str_replace('.', '/', $package_name).'/'.$activity_name.'.java';
	system('echo package '.$package_name.'\; > '.$source_file);
	system('cat '.PLUGIN_DIR.'android/'.$activity_name.'.java >> '.$source_file);
	
	echo "Converting strings.xml...\n";
	system('cd '.$output_path.'/res/values/; cat strings.xml | sed -r \'s/'.$activity_name.'/'.$title.'/g\' > strings-new.xml; rm strings.xml; mv strings-new.xml strings.xml');
	
	echo "Generating AndroidManifest.xml...\n";
	$manifest = file_get_contents($manifest_file);
	// replace markers
	$manifest = str_replace('###PACKAGE_NAME###', $package_name, $manifest);
	$manifest = str_replace('###VERSION_CODE###', $version_code, $manifest);
	$manifest = str_replace('###VERSION_NAME###', $version_name, $manifest);
	$manifest = str_replace('###TARGET_SDK###', $target_sdk, $manifest);
	$manifest = str_replace('###MIN_SDK###', $min_sdk, $manifest);
	$manifest = str_replace('###ACTIVITY_NAME###', $activity_name, $manifest);
	// write file
	$manifest_handler = fopen($output_path.'/AndroidManifest.xml', 'w');
	if ($manifest_handler != false) {
		fwrite($manifest_handler, $manifest);
		fclose($manifest_handler);
	} else {
		echo "Unable to write manifest file! Permission problem?\n";
		return false;
	}
	
	echo "Copying assets...\n";
	system('mkdir '.$output_path.'/assets');
	system('cp -r '.$input_path.'/* '.$output_path.'/assets');
	
	// user defined image
	if ($android_launcher_image != '') {
		if (file_exists($android_launcher_image)) {
            if (check_exists_command('convert')) {
                echo "Converting images...ldpi...";
                system('convert '.$android_launcher_image.' -resize '.RESOLUTION_LDPI.' '.$output_path.'/res/drawable-ldpi/ic_launcher.png');
                echo "...mdpi...";
                system('convert '.$android_launcher_image.' -resize '.RESOLUTION_MDPI.' '.$output_path.'/res/drawable-mdpi/ic_launcher.png');
                echo "...hdpi...";
                system('convert '.$android_launcher_image.' -resize '.RESOLUTION_HDPI.' '.$output_path.'/res/drawable-hdpi/ic_launcher.png');
                echo "...xhdpi...";
                system('convert '.$android_launcher_image.' -resize '.RESOLUTION_XHDPI.' '.$output_path.'/res/drawable-xhdpi/ic_launcher.png');
                echo "ok\n";
			} else {
                echo ">convert< not found! Skipping launcher images.\n";
			}
		} else {
			echo "Specified image does not exist.\n";
		}
	}
	
	echo "Building project...\n";
	
	system('cd '.$output_path.'; ant debug;');
	
	return true;
}

?>
