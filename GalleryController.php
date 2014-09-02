<?php

if (!defined('IN_CMS')) { exit(); }

class GalleryController extends PluginController {

	public function __construct() {
		$this->setLayout('backend');
		$this->assignToLayout('sidebar', new View('../../plugins/gallery/views/sidebar'));
	}
	
	public function index() {
		$this->display('gallery/views/index');
	}
	
	// All images of an album
	public function album($id = null) {
		$album = GalleryAlbum::findById($id);
		if($album) {
			$this->display('gallery/views/album', array('album' => $album));
		} else {
			redirect(get_url('plugin/gallery/index'));
		}
	}
	
	// Save new ordering of all images in an album
	public function image_reorder() {
		if(isset($_POST['data'])) {
			foreach($_POST['data'] as $key => $value) {
				$img = GalleryImage::findById($value);
				if($img) {
					$img->position = $key;
					$img->save();
				}
			}
		}
	}
	
	// Save new ordering of all albums
	public function album_reorder() {
		if(isset($_POST['data'])) {
			foreach($_POST['data'] as $key => $value) {
				$album = GalleryAlbum::findById($value);
				if($album) {
					$album->position = $key;
					$album->save();
				}
			}
		}
	}
	
	// Add an album
	public function add() {
		// $this->edit();
		$album = new GalleryAlbum($_POST);
		if($album) {
			if(isset($_POST['name'], $_POST['description'])) {
				// $album->setFromData($_POST);
				if(trim($album->name) != '') {
					$album->position = GalleryAlbum::getNextPosition();
					$album->save();
					if($_FILES['image']['tmp_name'] != '') {
						$this->_removeDirectory($album->getPathAbsolute());
						$this->_createImages($_FILES['image']['tmp_name'], $album->getPathAbsolute());
					}
					redirect(get_url('plugin/gallery/index'));
				}
			}
			$this->display('gallery/views/edit', array('album' => $album));
		} else {
			redirect(get_url('plugin/gallery/index'));
		}
	}
	
	// Edit album
	public function edit($id = null) {
		if($id === null) {
			$this->add();
			// redirect(get_url('plugin/gallery/index'));
		} else {
			$album = GalleryAlbum::findById($id);
			if($album) {
				if(isset($_POST['name'], $_POST['description'])) {
					$album->setFromData($_POST);
					if($_FILES['image']['tmp_name'] != '') {
						$this->_removeDirectory($album->getPathAbsolute());
						$this->_createImages($_FILES['image']['tmp_name'], $album->getPathAbsolute());
					}
					if(trim($album->name) != '') {
						$album->save();
						redirect(get_url('plugin/gallery/index'));
					}
				}
				$this->display('gallery/views/edit', array('album' => $album));
			} else {
				redirect(get_url('plugin/gallery/index'));
			}
		}
	}
	
	// Delete album
	public function delete($id) {
		$album = GalleryAlbum::findById($id);
		if($album) {
			if(isset($_POST['id']) AND $_POST['id'] == $id) {
				foreach(GalleryImage::findByAlbumId($album->id) as $image) {
					$this->remove($image->id, false);
				}
				$this->_removeDirectory($album->getPathAbsolute());
				$album->delete();
				redirect(get_url('plugin/gallery/index'));
			}
			$this->display('gallery/views/delete', array('album' => $album));
		} else {
			redirect(get_url('plugin/gallery/index'));
		}
	}
	
	// Delete image
	public function remove($id = null, $redirect = true) {
		$image = GalleryImage::findById($id);
		if($image) {
			$this->_removeDirectory($image->getPathAbsolute());
			$image->delete();
			if($redirect === true) {
				redirect(get_url('plugin/gallery/album/'.$image->album_id));
			}
		}
	}
	
	public function upload($id = null) {
		$album = GalleryAlbum::findById($id);
		if($album) {
			if(isset($_FILES['upl'], $_POST['album_id']) AND $album_id = $id) {
				
				$add = new GalleryImage();
				$add->album_id = $_POST['album_id'];
				$add->save();
				
				$this->_createImages($_FILES['upl']['tmp_name'], $add->getPathAbsolute());
				
				$img = new Image($_FILES['upl']['tmp_name']);
				$title = $img->getData('exif', Plugin::getSetting('exif_title_field', 'gallery')); // ImageDescription
				$description = $img->getData('exif', Plugin::getSetting('exif_description_field', 'gallery')); // UserComment
				unset($img);
				if(Plugin::getSetting('filename_as_title', 'gallery') == 'true') {
					$add->name = substr($_FILES['upl']['name'], 0, strrpos($_FILES['upl']['name'], '.'));
				}
				if(Plugin::getSetting('detect_title', 'gallery') == 'true' AND $title) {
					$add->name = $title;
				}
				if(Plugin::getSetting('detect_description', 'gallery') == 'true') {
					$add->description = $description;
				}
				$add->position = GalleryImage::getNextPosition($_POST['album_id']);
				$add->save();
				echo 'success';
			} else {
				$this->display('gallery/views/upload', array('album' => $album));
			}
		} else {
			redirect(get_url('plugin/gallery/index'));
		}
	}
	
	public function image($id = null) {
		$image = GalleryImage::findById($id);
		if($image) {
			if(get_request_method() == 'POST') {
				$image->setFromData($_POST);
				$image->save();
				if(isset($_POST['next'])) {
					$next = $image->getNextImg();
						if($next) {
							redirect(get_url().'plugin/gallery/image/'.$next->id);
						}
				}
			}
			$this->display('gallery/views/image', array('image' => $image));
		} else {
			redirect(get_url('plugin/gallery/index'));
		}
	}
	
	function settings() {
		if(get_request_method() == 'POST') {
			$_POST['settings']['detect_title'] = isset($_POST['settings']['detect_title']) ? 'true' : 'false';
			$_POST['settings']['detect_description'] = isset($_POST['settings']['detect_description']) ? 'true' : 'false';
			$_POST['settings']['add_route'] = isset($_POST['settings']['add_route']) ? 'true' : 'false';
			
			$images = array();
			foreach(isset($_POST['img_settings']) ? $_POST['img_settings'] : array() as $img) {
				if(
					isset($img['name'], $img['method'], $img['width'], $img['height'], $img['quality']) AND // , $img['type']
					$img['name'] != ''
				) {
					$img['retina'] = isset($img['retina']) ? 'true' : 'false';
					$images[] = $img;
				}
			}
			$_POST['settings']['images'] = json_encode($images);
			if($_POST['settings']['path']) {
				$_POST['settings']['path'] = trim($_POST['settings']['path'], '/') . '/';
			}
			
			foreach($_POST['settings'] as $key => $value) {
				Plugin::setSetting($key, $value, 'gallery');
			}
			Flash::setNow('success', 'Settings were updated successfully');
		}
		$this->display('gallery/views/settings', array(
			'settings' => Plugin::getAllSettings('gallery')
		));
	}
	
	function frontend($album_id = null, $image_id = null) {
		$album = GalleryAlbum::findById($album_id);
		if($album) {
			$pageId = Page::findById(Plugin::getSetting('route_page_id', 'gallery')) ? Plugin::getSetting('route_page_id', 'gallery') : 1;
			if($image_id !== null) {
				$image = GalleryImage::findOne(array(
					'where' => ' id = :id AND album_id = :album_id ',
					'values' => array(
						':id' => $image_id,
						':album_id' => $album_id
					)
				));
				if($image) {
					// Observer::observe('page_before_execute_layout', 'gallery_layout_observer');
					// function gallery_layout_observer($layout) {
					// 	$layout->assign('assign', 'true');
					// 	$layout->assignToLayout('assignToLayout', 'true');
					// }
					$_GET['gallery']['album_id'] = $album_id;
					$_GET['gallery']['image_id'] = $image_id;
					$page = Page::findById($pageId);
					$page->_executeLayout();
					exit;
				} else {
					pageNotFound();
				}
			} else {
				$_GET['gallery']['album_id'] = $album_id;
				$_GET['gallery']['image_id'] = $image_id;
				$page = Page::findById($pageId);
				$page->_executeLayout();
				exit;
			}
		} else {
			pageNotFound(); 
		}
	}
	
	private function _createImages($filename, $path) {
		if($path AND !is_dir($path)) {
			mkdir($path, 0777, true);
		}
		$images = (array) json_decode(Plugin::getSetting('images', 'gallery'));
		foreach($images as $imgSetting) {
			$img = new Image($filename);
			if($imgSetting->retina == 'true') {
				$img->{$imgSetting->method}(min(intval($imgSetting->width)*2, $img->getData('width')), min(intval($imgSetting->height)*2, $img->getData('height')));
				$img->saveJPG($path.$imgSetting->name.'@2x.jpg', intval($imgSetting->quality));
			}
			if($imgSetting->method != '') {
				$img->{$imgSetting->method}(intval($imgSetting->width), intval($imgSetting->height));
			}
			$img->saveJPG($path.$imgSetting->name.'.jpg', intval($imgSetting->quality));
			unset($img);
		}
	}
	
	private function _removeDirectory($dir) {
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
	}
	
}