<?php

class GalleryImage extends Record {

	const TABLE_NAME = 'gallery_image';
	
	public $id;
	public $album_id;
	public $name;
	public $description;
	public $position;
	
	public function getColumns() {
		return array('id', 'album_id', 'name', 'description', 'position');
	}
	
	public function getNextImg() {
		return GalleryImage::findOne(array(
			'where' => 'position > '.intval($this->position).' AND album_id = '.intval($this->album_id),
			'order' => ' position ASC'
		));
	}
	
	public function getPrevImg() {
		return GalleryImage::findOne(array(
			'where' => 'position < '.intval($this->position).' AND album_id = '.intval($this->album_id),
			'order' => 'position DESC'
		));
	}
	
	public function getPathAbsolute() {
		return ($this->id == '' OR $this->id == 0) ? null : CMS_ROOT . '/public/' . rtrim(Plugin::getSetting('path', 'gallery'), '/') . '/images/' . $this->id . '/';
	}
	
	public function getPath() {
		return ($this->id == '' OR $this->id == 0) ? null : URI_PUBLIC . 'public/' . rtrim(Plugin::getSetting('path', 'gallery'), '/') . '/images/' . $this->id . '/';
	}
	
	public function getUrl() {
		if(Plugin::getSetting('add_route', 'gallery') == 'true') {
			$page = Page::findById(Plugin::getSetting('route_page_id', 'gallery'));
			if($page) {
				return $page->url().$this->album_id.'/'.$this->id.'/';
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
				if($entry != "." && $entry != "..") {
					return $this->getPath() . $entry;
				}
			}
			closedir($handle);
		}
		return null;
	}
	
	public function getPosition() {
		return Record::countFrom(__CLASS__, ' album_id = ? AND position <= ?', array($this->album_id, $this->position));
	}
	
	public function getCount() {
		return Record::countFrom(__CLASS__, ' album_id = ?', array($this->album_id));
	}
	
	public static function getNextPosition($id) {
		return Record::countFrom(__CLASS__, ' album_id = ? ', array($id));
	}
	
	public static function findByAlbumId($id) {
		return self::find(array(
			'where' => 'album_id = '.intval($id),
			'order' => 'position'
		));
	}
	
	/* Native support in WolfCMS version 0.8.0
	public static function findById($id) {
		return Record::findByIdFrom(__CLASS__, $id);
	}
	//*/
	
	//* not needed
	public static function findAll() {
		return Record::findAllFrom(__CLASS__);
	}
	//*/
	
}

?>