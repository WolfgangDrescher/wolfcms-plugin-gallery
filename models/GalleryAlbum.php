<?php

class GalleryAlbum extends Record {

	const TABLE_NAME = 'gallery_album';
	
	public $id;
	public $name;
	public $description;
	public $position;
	
	public function getPathAbsolute() {
		return ($this->id == '' OR $this->id == 0) ? null : CMS_ROOT . '/public/' . rtrim(Plugin::getSetting('path', 'gallery'), '/') . '/albums/' . $this->id . '/';
	}
	
	public function getPath() {
		return ($this->id == '' OR $this->id == 0) ? null : URI_PUBLIC . 'public/' . rtrim(Plugin::getSetting('path', 'gallery'), '/') . '/albums/' . $this->id . '/';
	}
	
	public function getUrl() {
		if(Plugin::getSetting('add_route', 'gallery') == 'true') {
			$page = Page::findById(Plugin::getSetting('route_page_id', 'gallery'));
			if($page) {
				return $page->url().$this->id.'/';
			}
		}
		return $this->getPath();
	}
	
	public function link($name = null, $attrs = array(), $imgPath = null) {
		return '<a href="'.(!$imgPath ? $this->getUrl($imgPath) : $this->getPath()).'" '.
			implode(' ', array_map(function ($v, $k) { return $k.'="'.$v.'"'; }, $attrs, array_keys($attrs))).
			'>'.html_encode($name === null ? $this->name : $name).'</a>';
	}
	
	public function getImage($name = null) {
		if(isset($_COOKIE['isRetina']) AND $_COOKIE['isRetina'] == 'true') {
			if(is_file($this->getPathAbsolute() . $name . '@2x.jpg')) {
				return $this->getPath() . $name . '@2x.jpg';
			}
		}
		if(is_file($this->getPathAbsolute() . $name . '.jpg')) {
			return $this->getPath() . $name . '.jpg';
		}
		if(is_dir($this->getPathAbsolute()) AND $handle = opendir($this->getPathAbsolute())) {
			while(false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					return $this->getPath() . $entry;
				}
			}
			closedir($handle);
		}
		return null;
	}
	
	public static function getNextPosition() {
		return Record::countFrom(__CLASS__);
	}
	
	/* Native support in WolfCMS version 0.8.0
	public static function findById($id) {
		return Record::findByIdFrom(__CLASS__, $id);
	}
	//*/
	
	public static function findAll() {
		return self::find(array(
			'order' => 'position'
		));
	}
	
}

?>