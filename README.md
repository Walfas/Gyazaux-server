Gyazaux server
==============

[Gyazo][G] server modified from Ben Alman's "[Gyazo.. on your own server][Ben]".

When used with [Gyazaux client][GC], allows uploads with custom file names and 
JPG files (previously only PNG, like regular Gyazo). 

Files uploaded with the same name will have a counter appended. e.g., 
attempting to upload `hi.jpg` while `hi1.jpg` and `hi2.jpg` exist will result 
in `hi3.jpg` being uploaded. `jpg`s and `png`s have different counters.

Uploads can optionally be sent to separate directories based on date.

### Dependencies
`gd` (for image creation) and `exif` (to check image type) need to be enabled 
in your `php.ini`. I'm not aware of any other dependencies at this time. 

[Ben]: http://benalman.com/news/2009/10/gyazo-on-your-own-server/
[G]: http://gyazo.com
[GC]: https://github.com/Walfas/Gyazaux

