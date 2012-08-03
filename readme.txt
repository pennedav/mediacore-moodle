     __  _____________   _______   __________  ____  ______
    /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
   / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/   
  / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___   
 /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/   
      Moodle TinyMCE/Content Filter Plugins 1.0 beta
   
Two plugins work together to give you a seamless MediaCore experience
right from within Moodle. The TinyMCE plugin gives you a new button while
editing content; click it and your videos are right there! Just select 
which one you'd like to embed, and we'll insert a link to it in your 
content. With the MediaCore Filter enabled, these text links get turned 
into the appropriate code so that your video appears right within your
page.
   
====
MediaCore Filter Installation:
====
1) Unzip the package into /path/to/moodle/filters/mediacore.
2) Go to Settings -> Site administration -> Plugins -> Filters and:
    a) Make sure that the "Multimedia plugins" filter is enabled.
    b) Enable the MediaCore filter; set your URL.
3) Optionally install the TinyMCE plugin to allow easier embedding.

====
MediaCore Insert (for TinyMCE) Installation:
====

1) Copy "mediacoreinsert" folder into TinyMCE's plugins folder e.g.
	/path/to/moodle/lib/editor/tinymce/tiny_mce/3.4.6/plugins/

2) Open mediacore.php and fill in the configuration value at the
	top of the file e.g.
	$mediacore_url = 'http://yourmediacore.mediacore.tv';

3) Now we need to let Moodle know about the TinyMCE plugin. Open:
	/editor/tinymce/lib.php

	a) At the bottom of the file, just above "return $params", add:
		// ADDED FOR MEDIACORE 
		$params['plugins'] .= ",mediacoreinsert";
		$params['theme_advanced_buttons3_add'] = $params['theme_advanced_buttons3_add'] . ",|,mediacoreinsert";

4) Add some content; see the MediaCore button; click it. You're go to go!

====
About
====

[MediaCore](http://mediacore.com/) is an online video platform for managing, 
encoding, monetizing and delivering video to mobile and desktop devices. 
MediaCore makes it easy for any organization to share video either publicly or 
privately and build an amazing user experience on both desktop and mobile 
browsers around their own content. 

Who's using Mediacore? More and more MediaCore powered sites are popping up all 
over the world. You can learn more about some of these sites here on our 
[MediaCore showcase](http://mediacore.com/why-mediacore).