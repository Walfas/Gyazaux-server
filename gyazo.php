<?PHP

/*
 * PHP upload for Gyazo - v1.0 - 10/6/2009
 * http://benalman.com/
 * 
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Licensed under the MIT license
 * http://benalman.com/about/license/
 */

// Disable all error reporting.
error_reporting(0);

// The local path in which images will be stored (change as neceesary).
$path = "/srv/www/gyazo/";

// The URI path at which images will be accessed (change as neceesary).
$uri  = "http://" . $_SERVER['HTTP_HOST'] . "/grab/";

// Get binary image data from HTTP POST.
$imagedata = $_POST['imagedata'];

// Generate the filename.
$filename = md5( "$imagedata" ) . ".png";

// Save the image.
$fp = fopen( "$path$filename", 'xb' );

// If image didn't already exist, and was created successfully:
if ( $fp ) {
  fwrite( $fp, $imagedata );
  fclose( $fp );
  
  // Compress the image (destroying any alpha transparency).
  $image = @imagecreatefrompng( "$path$filename" );
  imagepng( $image, "$path$filename", 9 );
  imagedestroy( $image );
}

// Return the image URI.
print "$uri$filename";

?>
