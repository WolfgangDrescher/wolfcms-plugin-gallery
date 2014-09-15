<?php

if (!defined('IN_CMS')) { exit(); }

Plugin::setInfos(array(
	'id' => 'gallery',
	'title' => __('Gallery'),
	'description' => __('Provides photo gallery.'),
	'version' => '1.1',
	'license' => 'MIT',
	'author' => 'Wolfgang Drescher',
	'website' => 'https://github.com/WolfgangDrescher/wolfcms-plugin-gallery',
	'update_url' => 'https://raw.githubusercontent.com/WolfgangDrescher/wolfcms-plugin-gallery/master/version.xml',
	'type' => 'both',
	'require_wolf_version' => '0.8.0'
));

// use_helper('ActiveRecord');

Plugin::addController('gallery', __('Gallery'), 'page_edit', true);

AutoLoader::addFile('GalleryAlbum', CORE_ROOT.'/plugins/gallery/models/GalleryAlbum.php');
AutoLoader::addFile('GalleryImage', CORE_ROOT.'/plugins/gallery/models/GalleryImage.php');

include 'Image/Image.php';

if(Plugin::getSetting('add_route', 'gallery') == 'true') {
	$galleryPage = Page::findById(Plugin::getSetting('route_page_id', 'gallery'));
	if($galleryPage AND $galleryPage->path() != '') {
		Dispatcher::addRoute(array(
			'/'.$galleryPage->path().'/:num' => 'plugin/gallery/frontend/$1',
			'/'.$galleryPage->path().'/:num/:num' => 'plugin/gallery/frontend/$1/$2'
		));
	}
}
