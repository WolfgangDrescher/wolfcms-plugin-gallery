Image
=====

This class allows you to manipulate (e.g. resizing and rotating for thumbnails), output or save images with the GD library.

— [Wolfgang Drescher](http://wolfgangdrescher.ch/)

Features
--------

- Support for JPGs, PNGs and GIFs
- Resize images with different methods e.g. `->resizeFill(…)`, `->resizeFit(…)`
- Method chaining in one line e.g. `Image::init(…)->resizeFill(…)->saveJPG(…);`
- Nicely designed error messages with Bootstrap

Requirements
------------

- A server running at least PHP version 5.3 and the GD library installed.
- Recommended: error messages are formated with [Bootstrap](http://getbootstrap.com/).

License
-------

This framework is standing under MIT licence. Feel free to use it, but please place a reference to my name or website.

Setup
-----

Include the Bootstrap stylesheet to your websites header. Either use the following Bootstrap CDN link, or [download it directly](http://getbootstrap.com/getting-started/#download) from their server.

	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

Include `Image.php` in your config PHP file. You can make the following configurations:

	// your config.php
	require_once 'Image.php';
	Image::$throwExceptions = false; // (default: true) disable error messages
	Image::$chmod = 0777; // (default: 0755) set the image access rights for saving
	Image::$addFileExtension = true; // (default: false) add file extension if forgotten in save method
	Image::$shrinkOnly = false; // (default: true) allow class to enlarge images bigger than the source

Remember always to put a leading zero when you set the access rights with `Image::$chmod` so PHP can interpret the passed mode as an octal number.
Note that the default value of `Image::$addFileExtension` is `false` to avoid problems when you add the filename into a database or if you continue using it in your code after saving the image.
I recommend setting `Image::$throwExceptions` to `false` in a productive environment.

Image.php
---------

Create a new image object from a passed filename or use directly the [`tmp_name`] from an uploaded image in `$_FILES`:

	$img = new Image('IMG0001.jpg');
	$img = new Image($_FILES['file']['tmp_name']);

You should **always unset** the PHP object variable if the image is not used anymore so PHP can free all image variables to not cause problems with the memory limit.

	$img = new Image($filename);
	// do some image manipulations
	unset($img); // or $img = null;

When you need the current width or height of the image you can use following methods, but remember that it will always return the **current** width and height of the image and not the sizes from the original source image.

	echo $currentWidth = $img->getWidth();
	echo $currentHeight = $img->getHeight();

If you need informations about the original file use the `->getData()` method. If no argument is passed the method returns an array with all data available. Pass `exif` as agrument go get an array with all Exif data or pass a section name (`COMPUTED`, `ANY_TAG`, `IFD0`, `THUMBNAIL`, `COMMENT`, `EXIF`) to get specific Exif data. Pass multiple arguments to get deep access to the data array. The method will return `null` if the array element could not be found.

	print_r($img->getData()); // prints an array with all available data
	echo $mime = $img->getData('mime');
	echo $copyright = $img->getData('IFD0')['Copyright']; // PHP 5.4
	echo $description = $img->getData('IFD0', 'ImageDescription');
	echo $copyright = $img->getData('exif', 'COMPUTED', 'Copyright');
	print_r($img->getData('exif'));

Resize an image with `->resizeDeform($w, $h)` to force the image to the passed width and height. The method will not keep the aspect ratio and will skew the image.

	$img->resizeDeform(300, 50); 

Use `->resizeFill($w, $h)` to resize an image to a spesific size but ensure that the image will keep the aspect ratio and will allways fill the full passed width and height (equal to `background-size: cover;` in CSS). No background will be seen but the image will be croped.

	// $img->resizeFill(300, 300);

With `->resizeFit($w, $h, $rgba)` you can resize an image so it fits into a spesific size. The method will keep the aspect ratio and ensure that the image will always be fully displayed without cropping the image (equal to `background-size: contain;` in CSS). Set the background color as third argument (optional, default is black) and treat it like `rgb()` or `rgba()` in CSS. Giving the background color a transparency will not work with JPGs, use PNG instead.

	$img->resizeFit(300, 300, array(0,0,0,0)); // Fully transparent background color

Resize an image with `->resizeWidth($w)` or `->resizeHeihgt($h);` to resize it to the defined value. The other edge will be calculated in the aspect ratio.

	$img->resizeWidth(300);
	// or
	$img->resizeHeihgt(300);

If you want to resize the longer edge to a specific value use:

	$img->resizeLongEdge(300);

Use `->resizeMax($w, $h)` to resize an image so it will never be bigger as the maximal defined width or height.

	$img->resizeMax(500, 750);

With `->resizeScale($percent)` you can scale an image down by the defined percent value.

	$img->resizeScale(50); // image will be half as big as before

The method `->rotate($angle, $rgb)` will rotate an image. The image itself will keep the size after rotating but a background will append which means the image sizes will grow to keep its rectangular shape.

	$img->rotate(45);

There are Shortcuts to rotate an image by 90 degree clockwise or counter clockwise: `->rotateClockwise()`, `->rotateCw()`, `->rotateRight()` and `->rotateCounterClockwise()`, `->rotateCCw()`, `->rotateLeft()`.

You can output an image directly as JPG, PNG or GIF. Note that `->output...()` should be the last method you call or chain because it will send the output directly to the browser. Use the following methods:

	$img->outputJPG(80); // pass the quality (0-100) as argument, default: 100. In most cases 85 should be more than enough. A better quality will make a bigger file size, but not always cause better viewable results
	$img->outputPNG(1); // pass the compression level from 0 (best, default) to 9 (worst)
	$img->outputGIF();

Or save an image with these methods:

	$filename = '/path/to/new/img';
	$img->saveJPG($filename . '.jpg', $quality);
	$img->savePNG($filename . '.png', $compression);
	$img->saveGIF($filename . '.gif');

You can use method chaining to make multiple image manipulations at the same time.

	$img = new Image($filename);
	$img->resizeFill(300, 300)->rotateRight()->rotateCw()->saveJPG($newfile, 80);

Use the static method `Image::init()` to create a new instance and chain methods in one line. If you want to save multiple images in one chain call `->loadImage()` between them to reinitialize the original image passed with the class constructor or save in order from the biggest to the smallest. Otherwise it will always take the current image with all done manipulations.

	// wrong
	Image::init($filename)->resizeFill(300, 300)->saveJPG('thumb.jpg', 75)->resizeLongEdge(1280)->saveJPG('img.jpg', 85);

`img.jpg` will be in a bad quality and a square because it takes the thumbnail and then stretches it to 1280px.

	// correct
	Image::init($filename)->resizeFill(300, 300)->saveJPG('thumb.jpg', 75)->loadImage()->resizeLongEdge(1280)->saveJPG('img.jpg', 85);
	
	// or
	Image::init($filename)->resizeLongEdge(1280)->saveJPG('img.jpg', 85)->resizeFill(300, 300)->saveJPG('thumb.jpg', 75);

**Remember to unset the image object with `unset($img);` when it is not used anymore!**

Thanks for using
----------------

Contribute to this repository and help to improve this framework by [fixing issues](https://github.com/WolfgangDrescher/Image/issues) and commenting them.