<?PHP
/* Modified from Ben Alman's "PHP upload for Gyazo" to allow custom 
 * file names and JPGs. Meant to be used with Gyazaux client.
 * https://github.com/Walfas/Gyazaux
 * ---------------------------------
 * Original copyright info:
 *
 * PHP upload for Gyazo - v1.2.1 - 3/13/2011
 * http://benalman.com/news/2009/10/gyazo-on-your-own-server/
 * 
 * Copyright (c) 2011 "Cowboy" Ben Alman
 * Licensed under the MIT license
 * http://benalman.com/about/license/
 */

// The local path in which images will be stored (change as necessary).
// You can also use dirname( __FILE__ ) . "/relative/to/this/script/";
$path = '/var/www/i/'; 	// '/srv/www/gyazo/';

// The URI path at which images will be accessed (change as necessary).
$uri = 'http://' . $_SERVER['HTTP_HOST'] . '/i/';

// Get name and type of uploaded file
$fileparts = pathinfo($_FILES['imagedata']['name']);
$orig_filename = $fileparts['filename']; // Gets file's name without extension
$filetype = exif_imagetype($_FILES['imagedata']['tmp_name']);

switch($filetype) {
case IMAGETYPE_PNG: $file_ext = '.png'; break;
case IMAGETYPE_JPEG: $file_ext = '.jpg'; break;
default: $file_ext = null; break;
}

// "imagedata" can be adjusted in the form-data name attr in gyazo's script
// configuration file. If it's non-existent or has no size, abort.
if (!isset($_FILES['imagedata']['error']) || 
	$_FILES['imagedata']['size'] < 1 || !$file_ext)
{
	echo $uri, 'invalid.png';
	exit;
}

// Generate a unique filename.
$i = 0;
$filename = $orig_filename . $file_ext;
while (file_exists($filepath = $path . '/' . $filename)) {
	$filename = $orig_filename . ++$i . $file_ext;
}

// Move the file. If moving the file fails, abort.
if ( !move_uploaded_file($_FILES['imagedata']['tmp_name'], $filepath) ) {
	echo $uri, 'error.png'; 
	exit;
}

// Compress the image (destroying any alpha transparency).
if ($filetype == IMAGETYPE_PNG) {
	$image = @imagecreatefrompng($filepath);
	imagepng($image, $filepath, 9);
	imagedestroy($image);
}

// Return the image URI.
echo $uri, $filename;

