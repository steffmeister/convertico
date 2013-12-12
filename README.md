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
is in the "php5-cli" package.

Usage
-----
Available parameters:
* help, show help
* input=[directory], directory where your HTML5 app is located
* title=[title], title of the app
* plugin=[target name], which plugin to use as target
* list-plugins, list available targets

So for example you could use this to convert your app to an apk:
> ./convertico.php --input=/home/user/mysuperhtmlapp --plugin=android

Targets
-------
Available output targets (for now, this list will be longer hopefully):

### android
convert html5 app to apk (plugin.android.php)

#### Requirements
* Android SDK (adjust convertico.config.php accordingly)

### firefoxos
package app for Firefox OS (plugin.firefoxos.php)

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

Future plans
------------
I am not sure if this is all possible, but it would be great to have a number
of targets like Android, Windows Phone, iOS, Ubuntu, Firefox OS, Blackberry,
Sailfish, and I am sure there is a number of mobile OS' which I have forgot.

License
-------
convertico is licensed under the terms of the GPLv2.
