<?php

if (!defined('IN_CMS')) { exit(); }

$dir = Plugin::getSetting('path', 'gallery');

if(is_dir($dir)) {
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ($files as $fileinfo) {
		$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
		$todo($fileinfo->getRealPath());
	}
	rmdir($dir);
}

/*
foreach(GalleryImage::findAll() as $image) {
	$image->delete();
}

foreach(GalleryAlbum::findAll() as $album) {
	$album->delete();
}
//*/

Plugin::deleteAllSettings('gallery');

$PDO = Record::getConnection();
$driver = strtolower($PDO->getAttribute(Record::ATTR_DRIVER_NAME));

if ($driver == 'sqlite') {
	$PDO->exec("DROP TABLE ".TABLE_PREFIX."gallery_album");
	$PDO->exec("DROP TABLE ".TABLE_PREFIX."gallery_image");
}

exit();