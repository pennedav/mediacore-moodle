```
     __  _____________   _______   __________  ____  ______
    /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
   / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/   
  / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___   
 /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/   

      Moodle TinyMCE Content & Filter Plugins 1.6b
```
   
These two plugins work together to give you a seamless MediaCore experience
right from within Moodle. 

1. The TinyMCE plugin gives you a button to embed public or private media links into any TinyMCE rich text editor instance.
2. The MediaCore Filter turnes these text links into the appropriate embed code so that your video appears right within your course content.

* Note: These plugins/add-ons are supported on Moodle v2.2, 2.3 and 2.4 only.

---
MediaCore TinyMCE Plugin Installation:
======================================

1. First, copy the ```lib/editor/tinymce/mediacore``` folder into the TinyMCE plugins folder: 
	```/path/to/moodle/lib/editor/tinymce/tiny_mce/{version}/plugins/```

2. Next, we need to let Moodle know about the TinyMCE plugin. Open:
	```/path/to/moodle/lib/editor/tinymce/lib.php```

3. At the bottom of the ```get_init_params``` function, just above ```return $params``` (for Moodle v2.2/2.3) or just above 
   ```editor_tinymce_plugin::all_update_init_params($params, $context, $options);``` (for Moodle v2.4), add the following lines of code:

	~~~~~~~
	// Added for MediaCore
	if (!isset($filters)) {
		$filters = filter_get_active_in_context($context);
	}
	if (array_key_exists('filter/mediacore', $filters)) {
		global $COURSE;
		$params['plugins'] .= ",mediacore";
		if (isset($params['theme_advanced_buttons3_add'])) {
			$params['theme_advanced_buttons3_add'] .= ",|,mediacore";
		} else {
			$params['theme_advanced_buttons3_add'] = ",|,mediacore";
		}
		$params['course_id'] = (isset($COURSE)) ? $COURSE->id : false;
	}
	~~~~~~~
  
---
MediaCore Filter and Local Plugin Installation:
==============================
1. Copy the ```/filter/mediacore``` folder into ```/path/to/moodle/filter/```
2. Copy the ```/local/mediacore``` folder into ```/path/to/moodle/local/```
3. Login to Moodle as an administrator. Navigate to ```Settings > Site administration > Notifications```
	* You should see that the ```MediaCore Media filter``` and ```MediaCore package libraries``` need "to be upgraded". 
	  If you have any previously installed MediaCore filter or Local Plugin, please delete them first.
	* Click on the "Upgrade Moodle database now" button at the bottom of this page to begin the upgrade.
4. After the filter and plugin have been installed, you will be directed to the MediaCore Local Plugin settings page.
	* If the settings update screen doesn't appear, see pt#8 below after installation.
5. Enter your MediaCore URL in the ```Your MediaCore URL``` field. Make sure the URL begins with "http://".
6. If you have any LTI external tools set up in Moodle, you can map each tool to a Moodle course on this settings screen as well. 
	* Mappings are setup based on the LTI tool's base url and your MediaCore URL.
	* Mapping an LTI domain to a Moodle course makes content in that domain available to be embedded in that course. Map as many LTI domains to each course as you need. 
	* Instructions for setting up LTI external tools domains in Moodle can be found [here](http://docs.moodle.org/23/en/External_tool_settings). 
	* Instructions for creating LTI domains in your MediaCore app can be found [here](http://support.mediacore.com/customer/portal/articles/869178-what-is-lti-integration-and-how-do-i-set-it-up-).
7. Enable the ```MediaCore filter```:
	* Go to ```Settings > Site administration > Plugins > Filters > Manage Filters``` and make sure that the ```MediaCore Media``` filter is set to ```On``` and applied to ```Content```.
8. Any time you want to change these settings, navigate to ```Settings > Site administration > Plugins > Local plugins > Mediacore pacakge libraries``` to update them.

---
Add some content in Moodle:
====================================
1. Add some content (i.e. a new news forum post) to any Moodle course.
2. Inside the TinyMCE rich text editor you will see a MediaCore button. Click it. 
3. If any LTI domains were mapped to this course (step #6 above), choose one in the dropdown to access it's media. If no LTI domain was mapped to this course, it will default to public media.
4. Find the media item you want to add in the list and click the "add" button to add it to the text area.
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
