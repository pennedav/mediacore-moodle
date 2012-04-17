<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/   
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___   
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/   
 *                        Content Filter
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');


/**
 * Find instances of MediaCore.tv links and replace the link with an iframe.
 */
class filter_mediacorefilter extends moodle_text_filter {
	function filter($text, array $options = array()) {
		global $CFG;

		if (!is_string($text) or empty($text)) {
			// non string data can not be filtered anyway
			return $text;
		}
		if (stripos($text, '</a>') === false) {
			// performance shortcut - all regexes bellow end with the </a> tag,
			// if not present nothing can match
			return $text;
		}

		$newtext = $text; // we need to return the original value if regex fails!
		
		/**
		 * TODO
		 * The following doesn't work correctly; we get an array with only letters:
		 *
		 * [0]=>"<a href="http://link.mediacore.tv/media/video">link</a>", 
		 * [1]=>"n", 
		 * [2]=>"N";
		 *
		 */
		$search = '/<a\s[^>]+http:\/\/([0-9A-Za-z])+\.mediacore\.tv\/([0-9A-Za-z])[^>]+>([0-9A-Za-z])+[^>]+>/';
		$newtext = preg_replace_callback($search, 'filter_mediacorefilter_callback', $newtext);
		
		if (empty($newtext) or $newtext === $text) {
			  // error or not filtered
			  mtrace('link empty');
			  unset($newtext);
			  return $text;
		}
		
		return $newtext;
	}

}

/**
 * Change links to MediaCore into embedded MediaCore videos
 *
 * @param  $link
 * @return string
 */
function filter_mediacorefilter_callback($link) {

	global $CFG;

	if (filter_mediacorefilter_ignore($link[0])) {
		return $link[0];
	}
	/**
	 *
	 * The following is a bit of a hack because I can't make the regex pass us
	 * the correct variables; see above.
	 *
	 * At this point, we don't need to do anything fancy parsing-wise. We're 
	 * just taking the whole URL that's linked to and tacking the "embed_player"
	 * bit onto the end of as a src of an iFrame.
	 *
	 * TODO fix the regex so that we can construct the URL from the passed array...
	 * IF we really care.
	 *
	 */
	$murl = explode('href=', $link[0]);
	$mcore = explode('"', $murl[1]);
	$embedlink = embeddable($mcore[1]);
	$height = $CFG->filter_mediacorefilter_media_height;
	$width	= $CFG->filter_mediacorefilter_media_width;
	$output = <<<OET
		<iframe src="$embedlink/embed_player?iframe=True" 
				width="$width" 
				height="$height" 
        frameborder="0"
        allowfullscreen="allowfullscreen" 
        mozallowfullscreen="mozallowfullscreen" 
        webkitallowfullscreen="webkitallowfullscreen">
		</iframe>
OET;
	return $output;
}

/**
 *
 * Because the API doesn't provide the embed parameters (URL etc) separately 
 * (obscures it in the iframe src in a string) and instead supplies a permalink
 * which can differ (e.g. podcasts) from the direct play URL, we need to  
 * intercept podcast URIs and reformat them so that the iframe can play it 
 * properly; in other words, if we pass the given permalink e.g.
 * 
 * http://demo.mediacore.tv/podcasts/imperial-rome/construction-imperial-rome
 *
 * straight through, the iframe comes up 404. We need to replace the "podcasts"
 * with "media" and strip out the second portion (third portion is the slug) of 
 * the URI entirely so that the above example would read: 
 *
 * http://demo.mediacore.tv/media/construction-imperial-rome
 *
 */
function embeddable($url) {
	$podcast = split("/podcasts/", $url);
	if($podcast[1]) {
		$uri = split("/", $podcast[1]);
		$url = $podcast[0] . "/media/" . $uri[1];
	}
	return $url;
}

/**
 * Should the current tag be ignored in this filter?
 * @param string $tag
 * @return bool
 */
function filter_mediacorefilter_ignore($tag) {
	if (preg_match('/class="[^"]*nomediaplugin/i', $tag)) {
		return true;
	} else {
		false;
	}
}











