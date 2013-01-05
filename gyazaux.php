<?PHP
/* Modified to allow custom file names and JPGs. 
 * Meant to be used with Gyazaux client.
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

// If true, uploaded files will be sent to directories based on date
$split_date = false;
$date_format = 'Y/m'; // See PHP's `date` function for formats

// The local path in which images will be stored (change as necessary).
// You can also use dirname( __FILE__ ) . "/relative/to/this/script/";
$path = '/var/www/i'; 

// The URI path at which images will be accessed (change as necessary).
$uri = 'http://' . $_SERVER['HTTP_HOST'] . '/i';

// Get name and type of uploaded file
$fileparts = pathinfo($_FILES['imagedata']['name']);
$orig_filename = $fileparts['filename']; // Gets file's name without extension
$filetype = exif_imagetype($_FILES['imagedata']['tmp_name']);

switch($filetype) {
case IMAGETYPE_PNG: $file_ext = '.png'; break;
case IMAGETYPE_JPEG: $file_ext = '.jpg'; break;
default: $file_ext = null; break;
}

// Abort if invalid file
if (!isset($_FILES['imagedata']['error']) || 
	$_FILES['imagedata']['size'] < 1 || !$file_ext)
{
	header('g', true, 400); // Invalid file
	exit;
}

// Make directory based on date
if ($split_date) {
	$date_path = date($date_format);
	if (!$date_path) {
		header('g', true, 500); // invalid date
		exit;
	}
	$path .= '/'.$date_path;
	if (!is_dir($path) && !mkdir($path,0775,true))
		header('g', true, 500); // could not make directory
}
else
	$date_path = '';

// Generate a unique filename
$i = 0;
if ($orig_filename === '')
	$filename = ++$i . $file_ext;
else
	$filename = $orig_filename . $file_ext;

while (file_exists($filepath = $path . '/' . $filename))
	$filename = $orig_filename . ++$i . $file_ext;

// Move the file. If moving the file fails, abort.
if ( !move_uploaded_file($_FILES['imagedata']['tmp_name'], $filepath) ) {
	header('g', true, 500); // Failed moving file
	exit;
}

// Compress the image (destroying any alpha transparency).
if ($filetype == IMAGETYPE_PNG) {
	$image = @imagecreatefrompng($filepath);
	imagepng($image, $filepath, 9);
	imagedestroy($image);
}

// Return the image URI.
$url = $uri . '/' . $date_path . '/' . $filename;
$url = preg_replace('#/+#','/',$url); // Remove extraneous slashes
echo $url;

