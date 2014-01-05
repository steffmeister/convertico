convertico
==========

Convert your finished HTML5 app to another format.

Description
-----------
The intention of convertico is, that if you already have a finished HTML5 app,
and you want to bring it to various platforms that you simply feed convertico
with your app and it spits out your finished eg apk.

Requirements
------------
It is a PHP script so you need the PHP5 CLI on your system. Under Ubuntu this
is in the "php5-cli" package. I think it has to be at least v5.3.
Your HTML5 app needs to be inside of a folder and there needs to be an
index.html file.

Usage
-----
Available parameters:
* --help, show help
* --input=[directory], directory where your HTML5 app is located
* --title=[title], title of the app
* --plugin=[target name], which plugin to use as target
* --list-plugins, list available targets
* --check, use with plugin to check the configuration of a plugin

So for example you could use this to convert your app to an apk:
> ./convertico.php --input=/home/user/mysuperhtmlapp --plugin=android

The result is generated in the ouput subfolder and in this example it would be
named "mysuperhtmlapp-android".

Targets
-------
Available output targets (for now, this list will be longer hopefully):

### android
convert html5 app to apk (plugin.android.php). After building the apk can be
found in the bin directory of the output folder.

#### Requirements
 * Android SDK (adjust convertico.config.php accordingly)
 * Some settings in the Android SDK manager
 * ANT Compiler (in Ubuntu this is in package "ant")

#### Parameters
Those are optional.
 * --android-package-name
 * --android-target-sdk, defaults to "android-19"
 * --android-min-sdk, defaults to target-sdk
 * --android-version-code, defaults to 1
 * --android-version-name, defaults to "1.0"
 * --android-manifest, if you want to override the manifest template
 * --android-launcher-image, to set an launcher icon

### firefoxos
Package app for Firefox OS (plugin.firefoxos.php)

#### Requirements
 * zip

#### Parameters
Those are optional
 * --description
 * --firefoxos-version-name, defaults to "1.0"

### Your own target
You can easily write your own target plugin. Following functions must be
implemented:
 * plugin_about()
   Return some description of the plugin (String).
 * plugin_get_version()
   Return version number of the plugin (String).
 * plugin_check_config()
   Return true/false wheter all requirements are accomplished (eg check for
   external programs or config constants, ... )
 * plugin_do_work($title, $input_path, $output_path)
   Do the conversion. Return true/false.

Return values
-------------
 * 0 = ok
 * 1 = No plugins found
 * 2 = Plugin directory not found
 * 3 = Specified plugin not found
 * 4 = Plugin misconfiguration, check config file
 * 5 = Input path invalid
 * 6 = Output path already exists
 * 7 = Error occurred during conversion
 * 8 = index.html not found


Future plans
------------
I am not sure if this is all possible, but it would be great to have a number
of targets like Android, Windows Phone, iOS, Ubuntu, Firefox OS, Blackberry,
Sailfish, and I am sure there is a number of mobile OS' which I have forgot.

License
-------
convertico is licensed under the terms of the GPLv2.

Contact
-------
You can contact me via software(at)steffmeister(dot)at.

Donations
---------
If you want to support the development or just want to say thanks, you can
donate to me via the following ways:
 * PayPal, please contact me (see above).
 * Bitcoin Wallet, 19oU8ZCEz444mrSexgxtpPKrhfxLW2bEAh
 * Litecoin Wallet, LbYw2BfUdRb5xvKDEf1axb4WFuzP3GfWV5

