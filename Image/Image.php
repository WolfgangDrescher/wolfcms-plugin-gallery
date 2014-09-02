<?php

/*!
---
Image.php
Wolfgang Drescher - wolfgangdrescher.ch
This class allows you to manipulate (e.g. resizing and rotating for thumbnails) output or saving images with the GD library.
...
*/

class ImageException extends Exception {}

class Image {
	
	// Public variables for class settings
	public static $throwExceptions = true;
	public static $chmod = 0755;
	public static $addFileExtension = false;
	public static $shrinkOnly = true;
	
	private $error = false; // Boolean value whether an error occurred or not
	private $filename = null; // Path to the image file
	private $img = null; // Stage image where all manipulations will be set to
	private $data = array(); // Array with informations about the file
	
	const GIF = IMAGETYPE_GIF; // 1
	const JPG = IMAGETYPE_JPEG; // 2
	const JPEG = self::JPG; // 2
	const PNG = IMAGETYPE_PNG; // 3
	
	// Returns new self as an object to enable method chaining in one line
	public static function init($filename) {
		return new self($filename);
	}
	
	// Checks filename and prepares image stage for manipulations
	public function __construct($filename) {
		try {
			if(file_exists($filename) AND is_file($filename)) {
				$this->filename = $filename;
				$this->loadImage();
			} else {
				throw new ImageException('File `' . $filename . '` does not exist.');
			}
		} catch (ImageException $e) {
			$this->error = true;
			if(self::$throwExceptions === true) {
				echo '<div class="alert alert-danger">';
				echo '<span class="glyphicon glyphicon-warning-sign fa fa-bug fa-spin"></span> ';
				echo htmlentities($e->getMessage());
				echo '</div>';
			}
		}
	}
	
	// Frees all image variables
	public function __destruct() {
		if(!$this->isError()) {
			if(is_resource($this->img)) {
				imagedestroy($this->img);
			}
			$this->img = null;
			unset($this->img);
		}
	}
	
	// Enables public access to the data detected with getimagesize()
	public function getData($key = null) {
		if(func_num_args()) {
			$data = $this->getData();
			foreach(func_get_args() as $key => $arg) {
				if(isset($data[$arg])) {
					$data = $data[$arg];
					if($key + 1 >= func_num_args()) {
						return $data;
					}
				} else {
					return null;
				}
			}
		}
		return $this->data;
	}
	
	// Checks if an error occurred
	public function isError() {
		return $this->error === false ? false : true;
	}
	
	// Sets the current stage image to the passed filename
	public function loadImage() {
		if($gis = getimagesize($this->filename, $info)) {
			$this->data['width'] = $gis[0];
			$this->data['height'] = $gis[1];
			$this->data['type'] = $gis[2];
			$this->data['mime'] = $gis['mime'];
			$this->data['channels'] = isset($gis['channels']) ? $gis['channels'] : null;
			$this->data['bits'] = isset($gis['bits']) ? $gis['bits'] : null;
			if($this->getData('type') == self::JPG) {
				$this->data = array_merge($this->data, exif_read_data($this->filename, 0, true));
				$this->data['exif'] = exif_read_data($this->filename);
			}
			// $this->data['info'] = $info; // iptcparse()
			switch($this->data['type']) {
				case self::GIF: $icf = 'imagecreatefromgif'; break;
				case self::PNG: $icf = 'imagecreatefrompng'; break;
				case self::JPG: default: $icf = 'imagecreatefromjpeg'; break;
			}
			ini_set('memory_limit', '128M');
			$this->img = $icf($this->filename);
		} else {
			throw new ImageException('File `' . $this->filename . '` is not an image.');
		}
		return $this;
	}
	
	// Returns the save path and adds the file extension if needed
	private function parseSavePath($path, $type = null) {
		return self::$addFileExtension === true ? (
			mb_substr($path, -strlen($this->getFileExtension($type))) === $this->getFileExtension($type) ?
				$path : $path . $this->getFileExtension($type)
		) : $path;
	}
	
	// Returns the file extension of image $type or $this->getData('type')
	private function getFileExtension($type = null) {
		return mb_strtolower(strtr(image_type_to_extension($type === null ? $this->getData('type') : $type, true), array(
			'.jpeg' => '.jpg' // replace .jpeg with .jpg in file extension
		)));
	}
	
	// Returns the width of the current stage image
	public function getWidth() {
		return $this->isError() ? false : imagesx($this->img);
	}
	
	// Returns the height of the current stage image
	public function getHeight() {
		return $this->isError() ? false : imagesy($this->img);
	}
	
	// Alias for self::outputJPG()
	public function outputJPEG() {
		return call_user_func_array('self::outputJPG', func_get_args());
	}
	
	// Outputs the stage image as JPG
	public function outputJPG($quality = 100) {
		if($this->isError()) return false;
		header('Content-Type: image/jpeg');
		@imagejpeg($this->img, null, $quality);
		return $this;
	}
	
	// Outputs the stage image as PNG
	public function outputPNG($compression = 0) {
		if($this->isError()) return false;
		header('Content-Type: image/png');
		imagesavealpha($this->img, true);
		@imagepng($this->img, null, $compression);
		return $this;
	}
	
	// Outputs the stage image as GIF
	public function outputGIF() {
		if($this->isError()) return false;
		header('Content-Type: image/gif');
		@imagegif($this->img);
		return $this;
	}
	
	// Alias for self::saveJPG()
	public function saveJPEG() {
		return call_user_func_array('self::saveJPG', func_get_args());
	}
	
	// Saves the stage image as JPG file
	public function saveJPG($path, $quality = 100) {
		if($this->isError()) return false;
		@imagejpeg($this->img, $this->parseSavePath($path, self::JPG), $quality);
		chmod($this->parseSavePath($path, self::JPG), self::$chmod);
		return $this;
	}
	
	// Saves the stage image as PNG file
	public function savePNG($path, $compression = 0) {
		if($this->isError()) return false;
		imagesavealpha($this->img, true);
		@imagepng($this->img, $this->parseSavePath($path, self::PNG), $compression);
		chmod($this->parseSavePath($path, self::PNG), self::$chmod);
		return $this;
	}
	
	// Saves the stage image as GIF file
	public function saveGIF($path) {
		if($this->isError()) return false;
		@imagegif($this->img, $this->parseSavePath($path, self::GIF));
		chmod($this->parseSavePath($path, self::GIF), self::$chmod);
		return $this;
	}
	
	// Parse image sizes to the biggest size possible without enlarging the source
	private function parseSize(&$width, &$height, &$dstX, &$dstY, &$dstWidth, &$dstHeight) {
		if(self::$shrinkOnly === true) {
			if($dstWidth > $this->getWidth()) {
				$c = $this->getWidth() / $dstWidth;
				$dstWidth = $this->getWidth();
				$dstHeight *= $c;
				$width *= $c;
				$height *= $c;
				$dstX *= $c;
				$dstY *= $c;
			}
			if($dstHeight > $this->getHeight()) {
				$c = $this->getHeight() / $dstHeight;
				$dstHeight = $this->getHeight();
				$dstWidth *= $c;
				$width *= $c;
				$height *= $c;
				$dstX *= $c;
				$dstY *= $c;
			}
		}
	}
	
	// Resizes stage image to the passed size arguments
	private function resize($width, $height, $dstX, $dstY, $dstWidth, $dstHeight, $bg = array()) {
		if($this->isError()) return false;
		$this->parseSize($width, $height, $dstX, $dstY, $dstWidth, $dstHeight);
		$tmp = imagecreatetruecolor($width, $height);
		$bg = array_values($bg);
		if(count($bg) >= 3) {
			$num = isset($bg[3]) ? ($bg[3] > 1 ? $bg[3] / 100 : $bg[3]) : 1;
			$alpha = max(0, min(127, intval(floatval($num) * (-127) + 127)));
			imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, $bg[0], $bg[1], $bg[2], $alpha));
		}
		imagecopyresampled($tmp, $this->img, $dstX, $dstY, 0, 0, $dstWidth, $dstHeight, $this->getWidth(), $this->getHeight());
		$this->img = $tmp;
		return $this;
	}
	
	// Resizes an image without keeping the aspect ratio (will skew)
	public function resizeDeform($width, $height) {
		if($this->isError()) return false;
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Resized image will fill the full sizes passed as arguments (image will be croped)
	public function resizeFill($width, $height) {
		if($this->isError()) return false;
		$cWidth = $this->getWidth() / $width;
		$cHeight = $this->getHeight() / $height;
		$c = ($cHeight < $cWidth)? $cHeight : $cWidth;
		$tmpWidth = $this->getWidth() / $c;
		$tmpHeight = $this->getHeight() / $c;
		return $this->resize($width, $height, ($width-$tmpWidth)/2, ($height-$tmpHeight)/2, $tmpWidth, $tmpHeight);
	}
	
	// Resized image will fit into the sizes passed as arguments (image will not be croped, but a background will be seen)
	public function resizeFit($width, $height, $bg = array(0, 0, 0)) {
		if($this->isError()) return false;
		$cWidth = $this->getWidth() / $width;
		$cHeight = $this->getHeight() / $height;
		$c = ($cHeight > $cWidth)? $cHeight : $cWidth;
		$tmpWidth = $this->getWidth() / $c;
		$tmpHeight = $this->getHeight() / $c;
		return $this->resize($width, $height, ($width-$tmpWidth)/2, ($height-$tmpHeight)/2, $tmpWidth, $tmpHeight, $bg);
	}
	
	// Resizes stage image to the passed width and calculates the correct height
	public function resizeWidth($width) {
		if($this->isError()) return false;
		$height = $width * $this->getHeight() / $this->getWidth();
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Resizes stage image to the passed height and calculates the correct width
	public function resizeHeight($height) {
		if($this->isError()) return false;
		$width = $height * $this->getWidth() / $this->getHeight();
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Resizes stage image to the passed maximum width and height
	public function resizeMax($width, $height) {
		if($this->isError()) return false;
		$cWidth = $this->getWidth() / $width;
		$cHeight = $this->getHeight() / $height;
		$c = ($cHeight > $cWidth)? $cHeight : $cWidth;
		$width = $this->getWidth() / $c;
		$height = $this->getHeight() / $c;
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Rezies long edge of stage image to the passed value and calculates size of the other edge
	public function resizeLongEdge($length) {
		if($this->isError()) return false;
		$cWidth = $this->getWidth() / $length;
		$cHeight = $this->getHeight() / $length;
		$c = ($this->getWidth() > $this->getHeight())? $cWidth : $cHeight;
		$width = $this->getWidth() / $c;
		$height = $this->getHeight() / $c;
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Resizes stage image by the passed percent value
	public function resizeScale($percent) {
		if($this->isError()) return false;
		$width = $this->getWidth() * $percent / 100;
		$height = $this->getHeight() * $percent / 100;
		return $this->resize($width, $height, 0, 0, $width, $height);
	}
	
	// Rotates stage image by the passed angle
	// The stage image itself will keep the size after rotating but a background will append
	// which means the image sizes will grow to keep its rectangular shape 
	public function rotate($angle, $bg = array(0,0,0)) {
		if($this->isError()) return false;
		$angle = $angle * (-1);
		$this->img = imagerotate($this->img, $angle, imagecolorallocate($this->img, $bg[0], $bg[1], $bg[2]));
		return $this;
	}
	
	// Rotates stage image by 90 degree clockwise
	public function rotateClockwise() {
		return $this->rotate(90);
	}
	
	// Rotates stage image by 90 degree counter clockwise
	public function rotateCounterClockwise() {
		return $this->rotate(-90);
	}
	
	// Alias for self::rotateClockwise()
	public function rotateRight() {
		return $this->rotateClockwise();
	}
	
	// Alias for self::rotateCounterclockwise()
	public function rotateLeft() {
		return $this->rotateCounterclockwise();
	}
	
	// Alias for self::rotateClockwise()
	public function rotateCw() {
		return $this->rotateClockwise();
	}
	
	// Alias for self::rotateCounterclockwise()
	public function rotateCCw() {
		return $this->rotateCounterclockwise();
	}
	
}