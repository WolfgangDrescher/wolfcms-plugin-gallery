<?php

if (!defined('IN_CMS')) { exit(); }

$PDO = Record::getConnection();
$driver = strtolower($PDO->getAttribute(Record::ATTR_DRIVER_NAME));

if ($driver == 'sqlite') {
	$PDO->exec("CREATE TABLE ".TABLE_PREFIX."gallery_album (
					id INTEGER NOT NULL PRIMARY KEY,
					name varchar(255) default NULL,
					description varchar(255) default NULL,
					position INTEGER
				)
	");
	
	$PDO->exec("CREATE TABLE ".TABLE_PREFIX."gallery_image (
					id INTEGER NOT NULL PRIMARY KEY,
					album_id INTEGER NOT NULL,
					name varchar(255) default NULL,
					description varchar(255) default NULL,
					position INTEGER
				)
	");
}

// Store settings new style
//*

$getSettings = Plugin::getAllSettings('gallery');
$getSettings = is_array($getSettings) ? $getSettings : array();

$settings = array(
	'path' => 'images/gallery/',
	'detect_title' => 'true',
	'exif_title_field' => 'ImageDescription',
	'detect_description' => 'true',
	'exif_description_field' => 'UserComment',
	'filename_as_title' => 'true',
	'add_route' => 'true',
	'route_page_id' => '1',
	'images' => '[{"name":"original","method":"","width":"","height":"","quality":"100","retina":"false"},{"name":"img","method":"resizeLongEdge","width":"1280","height":"720","quality":"85","retina":"true"},{"name":"thumb","method":"resizeFill","width":"300","height":"300","quality":"85","retina":"true"}]'
);

Plugin::setAllSettings(array_merge($settings, $getSettings), 'gallery');
//*/