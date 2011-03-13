<?PHP

/*
 * PHP upload for Gyazo - v1.2.1 - 3/13/2011
 * http://benalman.com/news/2009/10/gyazo-on-your-own-server/
 * 
 * Copyright (c) 2011 "Cowboy" Ben Alman
 * Licensed under the MIT license
 * http://benalman.com/about/license/
 */

// The local path in which images will be stored (change as necessary).
// You can also use dirname( __FILE__ ) . "/relative/to/this/script/";
$path = '/srv/www/gyazo/';

// The URI path at which images will be accessed (change as necessary).
$uri  = 'http://' . $_SERVER['HTTP_HOST'] . '/grab/';

// "imagedata" can be adjusted in the form-data name attr in gyazo's script
// configuration file. If it's non-existent or has no size, abort.
if (!isset($_FILES['imagedata']['error']) || $_FILES['imagedata']['size'] < 1) {
  echo $uri, 'invalid.png';
  exit;
}

// Generate a unique filename.
$i = 0;
do {
  $filename = substr(md5(time() . $i++), -6) . '.png';
  $filepath = "$path$filename";
} while ( file_exists($filepath) );

// Move the file. If moving the file fails, abort.
if ( !move_uploaded_file($_FILES['imagedata']['tmp_name'], $filepath) ) {
  echo $uri, 'error.png'; 
  exit;
}

// Compress the image (destroying any alpha transparency).
$image = @imagecreatefrompng($filepath);
imagepng($image, $filepath, 9);
imagedestroy($image);

// Return the image URI.
echo $uri, $filename;

?>