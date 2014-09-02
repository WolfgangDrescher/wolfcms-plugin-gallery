Wolf CMS Gallery Plugin
======================

This plugin adds a gallery to the [Wolf CMS](http://wolfcms.org/).

— [Wolfgang Drescher](http://wolfgangdrescher.ch/)

Features
--------

- Set own upload images and thumbnails and their sizes. It is possible the define multiple files!
- Uploaded images will resize with the [Image.php](https://github.com/
WolfgangDrescher/Image) class.
- Autodetection of Exif data from JPGs for name and description of the image.
- Multi File Upload with [jQuery File Upload](https://github.com/blueimp/jQuery-File-Upload).
- Drag and Drop to reorder albums and images.
- Support for images with retina resolution.
- URL Routing for direct album and image links.

Requirements
------------

- WolfCMS version 0.8.0
- PHP version 5.3

License
-------

This framework is standing under MIT licence. Feel free to use it, but please place a reference to my name or website.

Setup
-----

- Copy this directory to `/wolf/plugins/` and rename it to `gallery`.
- Enable the plugin in the Administration section.
- Go to the gallery settings and set the page id of your gallery page if you want URL routing.

Usage
-----

In your gallery page you can use the methods `GalleryAlbum::findAll()` and `GalleryImage::findByAlbumId($albumId)` to fetch albums und images from the database.
Use in your gallery page the variables `$_GET['gallery']['album_id']` and `$_GET['gallery']['image_id']` to get the routes from the dispatcher. Example:

	<?php
	if($_GET['gallery']['album_id'] === null AND $_GET['gallery']['image_id'] === null) {
		// View with all albums
		echo '<h2>Albums</h2>';
		foreach(GalleryAlbum::findAll() as $album) {
			if(count(GalleryImage::findByAlbumId($album->id))) {
				echo '<h3>'.$album->link().'</h3>';
				echo '<p>'.$album->description.'</p>';
				if($album->getImage()) {
					echo '<img src="'.$album->getImage('album').'"/>';
				}
			}
		}
	} elseif($_GET['gallery']['album_id'] !== null AND $_GET['gallery']['image_id'] === null) {
		// View with all images of an album
		$album = GalleryAlbum::findById($_GET['gallery']['album_id']);
		$images = GalleryImage::findByAlbumId($_GET['gallery']['album_id']);
		if($album AND count($images)) {
			echo '<h2>'.$album->name.'</h2>';
			foreach($images as $image) {
				echo '<h3>'.$image->link().'</h3>';
				echo '<img src="'.$image->getImage('thumb').'"/>';
			}
		}
	} elseif($_GET['gallery']['album_id'] !== null AND $_GET['gallery']['image_id'] !== null) {
		// View of an image
		$image = GalleryImage::findById($_GET['gallery']['image_id']);
		if($image AND $image->album_id == $_GET['gallery']['album_id']) {
			echo '<h2>'.$image->name.'</h2>';
			echo '<p>'.$image->description.'</p>';
			if($image->getImage()) {
				echo '<img src="'.$image->getImage('img').'"/>';
			}
		}
	}
	?>

If you want to support retina images add this JavaScript code to your website:

	<script type="text/javascript">
	(function() {
		var isRetina = function() {
			return window.devicePixelRatio > 1 || (
				window.matchMedia &&
				window.matchMedia(
					"(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches
				);
		};
		if(isRetina()) {
			var date = new Date();
			date.setTime(date.getTime() + (60 /* days */ * 24 * 60 * 60 * 1000));
			document.cookie = 'isRetina=true; expires=' + date.toUTCString() + '; path=/';
		}
	})();
	</script>

Thanks for using
----------------

Contribute to this repository and help to improve this plugin by [fixing issues](https://github.com/WolfgangDrescher/wolfcms-plugin-gallery/issues) and commenting them.