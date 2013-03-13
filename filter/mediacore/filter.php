<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * Automatic media embedding filter class.
 *
 * @package    filter
 * @subpackage mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Invalid access');
require_once($CFG->dirroot . '/local/mediacore/lib.php');
require_once($CFG->libdir . '/filelib.php');


/**
 * Find instances of MediaCore.tv links and replace the link with embed code
 * from the MediaCore API.
 */
class filter_mediacore extends moodle_text_filter {

    private $_mediacore_url;
    private $_mcore_media;

    public function __construct($context, array $localconfig) {
        $this->_mediacore_url = local_mediacore_fetch_lti_url();
        $this->_mcore_media = new mediacore_media($this->_mediacore_url);
        parent::__construct($context, $localconfig);
    }

    public function filter($html, array $options = array()) {

        if (!is_string($html) || empty($html) || stripos($html, '</a>' === FALSE) ||
            strpos($html, $this->_mediacore_url) === FALSE) { //performance hack
                return $html;
        }

        $dom = new DomDocument();
        $dom->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"));
        $xpath = new DOMXPath($dom);
        foreach($xpath->query('//a') as $node) {
            $href = $node->attributes->getNamedItem('href')->nodeValue;
            if (stripos($href, $this->_mediacore_url) !== FALSE) {
                $new_node  = $dom->createDocumentFragment();
                $new_node->appendXML($this->_fetch_embed_code($href));
                $node->parentNode->replaceChild($new_node, $node);
            }
        }
        return $dom->saveHTML($dom->documentElement);
    }

    /**
     * Change links to MediaCore into embedded MediaCore videos
     */
    private function _fetch_embed_code($href) {
        global $COURSE;
        $type_id = null;
        $msg = get_string('filter_no_video_found', 'filter_mediacore');

        // Parse the link so we can get to the slug and type_id (if applicable).
        $uri_components = parse_url($href);
        $path_arr = explode('/', $uri_components['path']);
        $slug = end($path_arr);
        foreach (explode('&', $uri_components['query']) as $kv) {
            $arr = explode('=', $kv);
            if (isset($arr[0]) && $arr[0] == 'type_id') {
                $type_id = (int)$arr[1];
                break;
            }
        }

        if ($type_id >= 0) {
            if ($type_id > 0) {
                $type_ids = local_mediacore_fetch_lti_tool_ids_by_course_id($COURSE->id);
                if (empty($type_ids)) {
                    $msg = get_string('filter_error_no_type_id', 'filter_mediacore');
                    return $this->_get_embed_error_html($msg);
                } elseif (!in_array($type_id, explode(',', $type_ids->value))) {
                    $msg = get_string('filter_no_type_mapping_error', 'filter_mediacore');
                    return $this->_get_embed_error_html($msg);
                }
            }
            // use the slug to query the MediaCore API to get the embed code (with signed lti params if applicable)
            $embed_html = $this->_mcore_media->fetch_media_embed($slug, $COURSE->id, $type_id);
            if ($embed_html) {
                return $embed_html;
            }
        }
        return $this->_get_embed_error_html($msg);
    }


    /**
     * @param {string} $msg The error message string
     * @return {string}
     */
    private function _get_embed_error_html($msg='') {
        if (empty($msg)) {
            $msg = get_string('filter_no_video_found', 'filter_mediacore');
        }
        return '<div class="mcore-no-video-found-error"><p>' . $msg . '</p></div>';
    }
}
