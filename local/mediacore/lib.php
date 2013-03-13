<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore's local plugin
 *
 * @package    local
 * @subpackage mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die('Invalid access');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/../../mod/lti/locallib.php');

/**
 * A Class that encapsulated results fetched from the media api endpoints
 */
class mediacore_media
{
	private $_course;
	private $_course_id;
	private $_curr_pg;
	private $_has_next_pg;
	private $_has_prev_pg;
	private $_limit;
	private $_media_api_url;
	private $_media_get_api_url;
	private $_mediacore_url;
	private $_mediacore_url_regex;
	private $_next_pg;
	private $_pg_count;
	private $_prev_pg;
	private $_rowSet;
	private $_rowset_count;
	private $_search;

	/**
	 * Constructor
	 * @param string $mediacore_url
	 */
	public function __construct($mediacore_url) {
		$this->_mediacore_url = rtrim($mediacore_url, '/');
		$this->_mediacore_url_regex = '/' . str_replace('/', '\/', $this->_mediacore_url) . '\/[^"]+/i';
		$this->_media_api_url = $mediacore_url . '/api/media';
		$this->_media_get_api_url = $mediacore_url . '/api/media/get';
	}

	/**
	 * Fetch media from the "/api/media" endpoint
	 * @param int $curr_pg
	 * @param string $search
	 * @param int $limit
	 * @param int|null $course_id
	 * @param int_null $type_id
	 * @return array
	 */
	public function fetch_media($curr_pg = 0, $search = '', $limit = 6, $course_id = null, $type_id = null) {

		$this->_curr_pg = $curr_pg; //zero-indexed
		$this->_search = $search;
		$this->_limit = $limit;

		$params = array(
			'type' => 'video',
			'limit' => $this->_limit,
			'offset' => $this->_curr_pg * $this->_limit,
			'search' => $this->_search,
		);

		$is_lti_request = (isset($course_id, $type_id) && $type_id > 0);
		if ($is_lti_request) {
			$params = $this->_get_signed_lti_params($this->_media_api_url, $course_id, $type_id, $params);
		}
		$result_obj = $this->_get_curl_response($this->_media_api_url, $params);
		if (empty($result_obj)) {
			return FALSE;
		}

		//build result data
		$this->_rowSet = $result_obj->media;
		$this->_rowset_count = $result_obj->count;
		$this->_pg_count = ceil($this->_rowset_count / $this->_limit);
		$this->_next_pg = $this->_curr_pg + 1;
		$this->_has_next_pg = ($this->_next_pg < $this->_pg_count);
		$this->_prev_pg = $this->_curr_pg - 1;
		$this->_has_prev_pg = ($this->_prev_pg >= 0);
		return $this->_rowSet;
	}

	/**
	 * Fetch a media item's embed code
	 * @param string $slug
	 * @param int|null $course_id
	 * @param int|null $type_id
	 * @return string
	 */
	public function fetch_media_embed($slug, $course_id = null, $type_id = null) {
		$params = array('slug' => $slug);
		$is_lti_request = (isset($course_id, $type_id) && $type_id > 0);
		if ($is_lti_request) {
			$params = $this->_get_signed_lti_params($this->_media_get_api_url, $course_id, $type_id, $params);
		}
		$result_obj = $this->_get_curl_response($this->_media_get_api_url, $params);
		if (empty($result_obj)) {
			return FALSE;
		}
		return ($is_lti_request)
			? $this->_get_embed_iframe_with_lti_params($result_obj->embed, $course_id, $type_id)
			: $result_obj->embed;
	}

	/**
	 * Get the embed html with signed lti parameters
	 * @param string $embed_html
	 * @param int $course_id
	 * @param int $type_id
	 * @return string
	 */
	private function _get_embed_iframe_with_lti_params($embed_html, $course_id, $type_id) {
		preg_match($this->_mediacore_url_regex, $embed_html, $matches);
		if (isset($matches[0])) {
			$url_parts = parse_url($matches[0]);
			$base_url = $this->_mediacore_url . $url_parts['path'];
			$params = array('iframe' => 'True');
			$lti_params = $this->_get_signed_lti_params($base_url, $course_id, $type_id, $params);
			$new_iframe_src = $base_url . '?' . $this->_encode_params($lti_params);
			$embed_html = str_replace($matches[0], $new_iframe_src, $embed_html);
		}
		return str_replace('&', '&amp;', $embed_html);
	}

	/**
	 * Fetch an api url using curl
	 * @param string $url
	 * @param array $params
	 * @param bool $debug
	 */
	private function _get_curl_response($url, $params=array(), $debug=FALSE) {
		$options = array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : '') . $this->_encode_params($params),
		);
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		if ($debug) {
			$this->_debug_curl($url, $encoded_params);
		}
		if (!$result = curl_exec($ch)) {
			return FALSE;
		}
		curl_close($ch);
		return json_decode($result);
	}

	/**
	 * Build the signed lti params
	 * @param string $endpoint
	 * @param int $course_id
	 * @param int $type_id
	 * @param array $params
	 * @return array
	 */
	private function _get_signed_lti_params($endpoint, $course_id, $type_id, $params=array()) {
		global $DB;

		$lti_type = $DB->get_record('lti_types', array('id' => $type_id), '*', MUST_EXIST);
		$type_config = lti_get_type_config($type_id);

		if ($this->_course_id != $course_id) {
			$this->_course_id = $course_id;
			$this->_course = $DB->get_record('course', array('id' => $this->_course_id), '*', MUST_EXIST);
		}

		$key = ''; $secret = '';
		if (!empty($type_config['resourcekey'])) {
			$key = $type_config['resourcekey'];
		}
		if (!empty($type_config['password'])) {
			$secret = $type_config['password'];
		}
		if (empty($key) || empty($secret)) {
			die('No key and/or secret, Oauth signature will fail!');
		}
		$request_params = local_mediacore_lti_build_request($lti_type, $type_config, $this->_course);
		return lti_sign_parameters(array_merge($request_params, $params), $endpoint, 'GET', $key, $secret);
	}

	/**
	 * Urlencode the url parameter values
	 * @param array $params
	 * @return string
	 */
	private function _encode_params($params) {
		$encoded_params = '';
		foreach ($params as $k=>$v) {
			$encoded_params .= "$k=" . urlencode($v) . "&";
		}
		return substr($encoded_params, 0 , -1);
	}

	/**
	 * Output the url used in the curl request
	 * @param string $url
	 * @param array $params
	 */
	private function _debug_curl($url, $params) {
		echo htmlentities($url . (strpos($url, '?') === FALSE ? '?' : '') . $params) . '<br/><br/>';
	}


	/**
	 * Instantiate a new mediacore_media_row object
	 * @param object $media
	 * @return object
	 */
	public function get_media_row($media) {
		return new mediacore_media_row($media);
	}

	/**
	 * Get the current zero-indexed page number
	 * @return int
	 */
	public function get_current_page() {
		return $this->_curr_pg;
	}

	/**
	 * Get the current human readable page number
	 * @return string
	 */
	public function get_current_page_str() {
		return '' . $this->_curr_pg + 1;
	}

	/**
	 * Get the previous page number
	 * @return int
	 */
	public function get_previous_page() {
		return $this->_prev_pg;
	}

	/**
	 * Get whether there is a previous page
	 * @return bool
	 */
	public function has_previous_page() {
		return $this->_has_prev_pg;
	}

	/**
	 * Get the next page number
	 * @return int
	 */
	public function get_next_page() {
		return $this->_next_pg;
	}

	/**
	 * Get whether there is a next page
	 * @return bool
	 */
	public function has_next_page() {
		return $this->_has_next_pg;
	}

	/**
	 * Get the page count
	 * @return int
	 */
	public function get_page_count() {
		return $this->_pg_count;
	}

	/**
	 * Get the page count string
	 * @return string
	 */
	public function get_page_count_str() {
		if ($this->_pg_count > 0) {
			return 'Page ' . $this->get_current_page_str() . ' of ' .
				$this->get_page_count();
		} else {
			return '';
		}
	}

	/**
	 * Get the search query string
	 * @return string
	 */
	public function get_search_query() {
		return $this->_search;
	}

	/**
	 * Get the rowset count
	 * @return int
	 */
	public function get_rowset_count() {
		return $this->_rowset_count;
	}
}



/**
 * A class that encapsulated a media row object
 */
class mediacore_media_row
{
	private $_duration;
	private $_publish_on;
	private $_row_obj;
	private $_thumbs;
	private $_title;
	private $_url;

	/**
	 * Constructor
	 * @param object $media
	 */
	public function __construct($media) {
		$this->_duration = $media->duration;
		$this->_publish_on = $media->publish_on;
		$this->_row_obj = $media;
		$this->_thumbs = $media->thumbs;
		$this->_title = $media->title;
		$this->_url = $media->url;
	}

	/**
	 * Get the media item's url
	 * @return string
	 */
	public function get_url() {
		/**
		 * Because the API doesn't provide the embed URL separately (obscures it in the
		 * iframe src) and instead supplies a permalink which can differ (e.g. podcasts)
		 * from the direct play URL, we need to intercept podcast URIs and reformat them
		 * so that the iframe can play it properly; if we pass the given permalink e.g.
		 *
		 * http://demo.mediacore.tv/podcasts/imperial-rome-and-ostia/the-construction-of-imperial-rome
		 *
		 * straight through, the iframe comes up 404. We need to replace the "podcasts"
		 * with "media" and strip out the second portion (third portion is the slug) of
		 * the URI entirely so that the above example would read:
		 *
		 * http://demo.mediacore.tv/media/the-construction-of-imperial-rome
		 */
		$podcast = explode("/podcasts/", $this->_url);
		if (isset($podcast[1]) && $podcast[1]) {
			$uri = explode("/", $podcast[1]);
			$this->_url = $podcast[0] . "/media/" . $uri[1];
		}
		return $this->_url;
	}

	/**
	 * Get the media item's small thumb url
	 * @return string
	 */
	public function get_thumbs_small_url() {
		return $this->_thumbs->s->url;
	}

	/**
	 * Get the title
	 * @return string
	 */
	public function get_title() {
		return htmlentities($this->_title, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Get the escaped title safe for js insertion
	 * @return string
	 */
	public function get_escaped_title() {
		return htmlentities(addslashes($this->_title), ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Get the duration in hh:mm:ss
	 * @return int
	 */
	public function get_duration() {
		return $this->_sec2hms($this->_duration, TRUE);
	}

	/**
	 * Get the publish on date relative to today
	 * @return string
	 */
	public function get_publish_on() {
		return $this->_relative_time(strtotime($this->_publish_on));
	}

	/**
	 * Convert seconds to hh:mm:ss
	 * @param int $sec
	 * @param bool $pad_hours
	 */
	private function _sec2hms ($sec, $pad_hours = FALSE)
	{
		$hms = "";
		$sec = (int)$sec;

		$hours = (int)($sec/3600);

		$hms .= ($pad_hours)
			? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
			: $hours. ":";

		$minutes = (int)(($sec / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
		$seconds = (int)($sec % 60);
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		return $hms;
	}

	/**
	 * Convert the time to relative time
	 * @param int $time
	 */
	private function _relative_time($time)
	{
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();
		$difference = $now - $time;
		$tense = "ago";

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);
		if ($difference != 1) {
			$periods[$j].= "s";
		}
		return "$difference $periods[$j] ago ";
	}
}
