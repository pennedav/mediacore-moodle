```
     __  _____________   _______   __________  ____  ______
    /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
   / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/   
  / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___   
 /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/   

      Moodle TinyMCE Content & Filter Plugins 1.5
```
   
These two plugins work together to give you a seamless MediaCore experience
right from within Moodle. 

1. The TinyMCE plugin gives you a button to embed public or private media links into any TinyMCE rich text editor instance.
2. The MediaCore Filter turnes these text links into the appropriate embed code so that your video appears right within your course content.

* Note: These plugins/add-ons are supported on Moodle v2.2 and v2.3.

---
MediaCore TinyMCE Plugin Installation:
======================================

1. First, copy the ```lib/editor/tinymce/mediacore``` folder into the TinyMCE plugins folder: 
	```/path/to/moodle/lib/editor/tinymce/tiny_mce/3.4.6/plugins/```

2. Next, we need to let Moodle know about the TinyMCE plugin. Open:
	```/path/to/moodle/lib/editor/tinymce/lib.php```

3. At the bottom of this file, just above ```return $params```, add the following:

	~~~~~~~
	// Added for MediaCore
	if (array_key_exists('filter/mediacore', $filters)) {
		global $COURSE;
	    $params['plugins'] .= ",mediacore";
    	$params['theme_advanced_buttons3_add'] = $params['theme_advanced_buttons3_add'] . ",|,mediacore";
	    $params['course_id'] = $COURSE->id;
	}
	~~~~~~~
  
---
MediaCore Filter and Local Plugin Installation:
==============================
1. Copy the ```/filters/mediacore``` folder into ```/path/to/moodle/filters/```
2. Copy the ```/local/mediacore``` folder into ```/path/to/moodle/local/```
2. Login to Moodle as an administrator. Navigate to ```Settings > Site administration > Notifications```
	* You should see that the ```MediaCore Media filter``` and ```MediaCore package libraries``` need "to be upgraded" (If you have any previously installed MediaCore filter or Local Plugin, please delete them first!).
	* Click on the "Upgrade Moodle database now" button at the bottom of this page to begin the upgrade.
3. After the filter and plugin have been installed, you will be directed to the MediaCore Local Plugin settings page.
4. Enter your MediaCore URL in the ```Your MediaCore URL``` field. Make sure the URL begins with "http://", and no trailing slash please!
5. If you have any LTI external tools set up in Moodle, you can map each tool to a Moodle course on this settings screen as well. 
	* Mapping an external LTI tool (domain) to a Moodle course makes content in that domain available to be embedded in that course. 
	* Instructions for setting up LTI external tools in Moodle can be found [here](http://docs.moodle.org/23/en/External_tool_settings). 
	* Instructions for creating LTI domains in your MediaCore app can be found [here](http://support.mediacore.com/customer/portal/articles/869178-what-is-lti-integration-and-how-do-i-set-it-up-).
6. Enable the ```MediaCore filter```:
	* Go to ```Settings > Site administration > Plugins > Filters > Manage Filters``` and make sure that the ```MediaCore Media``` filter is set to ```On``` and applied to ```Content```.
7. Make sure the ```MediaCore local plugin``` is available:
	* Go to ```Settings > Site administration > Plugins > Local plugins > Manage local plugins``` and make sure that the ```MediaCore package libraries``` is available in the list of plugins.
8. Any time you want to change these settings, navigate to ```Settings > Site administration > Plugins > Local plugins > Mediacore pacakge libraries```, to update them.

---
Finally, add some content in Moodle:
====================================
1. In Moodle, go to any course, and add some content (i.e. a new news forum post).
2. Inside the TinyMCE rich text editor you will see a MediaCore button. Click it. 
3. Choose "public" or any "LTI connection" (if applicable) form the popup windows dropdown menu. 
4. Find the media item you want to add and click the "add" button to add it.
5. When viewing the newly added content, any links to MediaCore videos will be dynamically embedded into the content.

---
About
=====

[MediaCore](http://mediacore.com/) is an online video platform for managing, 
encoding, monetizing and delivering video to mobile and desktop devices. 
MediaCore makes it easy for any organization to share video either publicly or 
privately and build an amazing user experience on both desktop and mobile 
browsers around their own content. 

Who's using Mediacore? More and more MediaCore powered sites are popping up all 
over the world. You can learn more about some of these sites here on our 
[MediaCore showcase](http://mediacore.com/why-mediacore).
