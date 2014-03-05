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
    
    /* check for gd extension */
    if (!extension_loaded('gd')) {
    	echo ">PHP gd extension< not loaded, resizing of icons will not be possible!\n";
    }
    
	return true;
}


function plugin_do_work($title, $input_path, $output_path) {

    /* get parameters */
    $longopts = array(
        "firefoxos-version-name:",
        "firefoxos-icon:",
        "description:"
    );
    
    $options = parseParameters($longopts);
    
    //print_r($options);

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
	
	/* handle icons */
	if (isset($options['firefoxos-icon']) && (extension_loaded('gd'))) {
		$icon_file = $options['firefoxos-icon'];
		//echo mime_content_type($icon_file);
		if (mime_content_type($icon_file) == 'image/png') {
			$manifest['icons'] = array();
			$orig_icon = imagecreatefrompng($icon_file);
			if ($orig_icon != false) {
				$width = imagesx($orig_icon);
				$height = imagesy($orig_icon);
				if ($width == $height) {
					mkdir($output_path.'/converticoicons');
					$manifest['icons'] = array();
					$gen_sizes = array(32, 60, 90, 120, 128, 256);
					foreach($gen_sizes as $new_width) {
						echo "Icon: generating icon with width ".$new_width."...";
						
						// imagescale will result in no alpha and wrong colors?!
						//$icon = imagescale($orig_icon, $new_width, $new_width);
						
						$icon = imagecreatetruecolor($new_width, $new_width);
						imagealphablending($icon, false);
					    imagesavealpha($icon, true);
					    $transparent = imagecolorallocatealpha($icon, 255, 255, 255, 127);
						imagefilledrectangle($icon, 0, 0, $width, $height, $transparent);
						imagecopyresampled($icon, $orig_icon, 0, 0, 0, 0, $new_width, $new_width, $width, $height);
						
						if ($icon != false) {
							if (imagepng($icon, $output_path.'/converticoicons/icon_'.$new_width.'.png')) {
								$manifest['icons'][$new_width] = '/converticoicons/icon_'.$new_width.'.png';
								echo "ok\n";
							} else {
								echo "saving failed\n";
							}
						} else {
							echo "scale failed\n";
						}
					}
				} else {
					echo "Icon: Icons need to be square!\n";
				}
			} else {
				echo "Unable to load icon.";
			}
		} else {
			echo "Icon: Only PNG is supported!\n";
		}
	}
	
	/* write manifest */
	$manifest_handler = fopen($output_path.'/manifest.webapp', 'w');
	fwrite($manifest_handler, json_readable_encode($manifest));
	fclose($manifest_handler);
	
	/* copy original to output path */
	system('cp -r '.$input_path.'/* '.$output_path);
	
	/* zip it up */
	system('cd '.$output_path.'; zip -rq '.$title.'.zip *');
	
	return true;
}

?>
