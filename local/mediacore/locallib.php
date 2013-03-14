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

//constants
define('MEDIACORE_LOCAL_PLUGIN_NAME', 'local_mediacore');
define('MEDIACORE_LOCAL_COURSELTI_SETTING_NAME', 'local_mediacore_courselti');
define('MEDIACORE_LOCAL_LTI_TOOL_STATE_CONFIGURED', 1);

/**
 * Course deletion event handler
 * @param object $course
 * @return bool
 */
function local_mediacore_course_delete_event_handler($course) {
    global $DB;
    $DB->delete_records('config_plugins',
        array(
            'plugin' => MEDIACORE_LOCAL_COURSELTI_SETTING_NAME,
            'name' => (string)$course->id,
        )
    );
    return TRUE;
}

/**
 * Helper to build select list HTML for available lti connection options
 * @param array $lti_tools
 * @param int $type_id
 * @return string
 */
function local_mediacore_build_connection_options($lti_tools, $type_id) {

    $selected = ($type_id === 0) ? 'selected ' : '';
    $html = '<option value="0" ' . $selected . '>' .
        get_string('tinymce_public_connection', 'local_mediacore') .
        '</option>';

    foreach ($lti_tools as $key => $t) {
        $selected = ($t->id == $type_id) ? 'selected ' : '';
        $html .= '<option value="' . $t->id . '" ' . $selected . '>';
        $html .= $t->name . ' (' .
            get_string('tinymce_lti_connection', 'local_mediacore') .
            ')</option>';
    }
    return $html;
}

/**
 * Fetch the mediacore lti site url from config
 * @return string
 */
function local_mediacore_fetch_lti_url() {
    global $DB;
    $config = $DB->get_record('config_plugins', array(
        'plugin' => MEDIACORE_LOCAL_COURSELTI_SETTING_NAME,
        'name' => 'mediacore_url',
    ));
    return (empty($config) || empty($config->value))
        ? 'http://demo.mediacore.tv'
        : (string)$config->value;
}

/**
 * Fetch all visible courses
 * @return array
 */
function local_mediacore_fetch_courses() {
    global $DB;
    $query = "SELECT *
        FROM {course}
    WHERE format != :format
    AND visible = :visible";
    return $DB->get_records_sql($query, array(
        'format' => 'site',
        'visible' => '1',
    ));
}

/**
 * Fetch available lti external tools based on the domain
 * @return array
 */
function local_mediacore_fetch_lti_tools() {
    global $DB;
    $query = "SELECT *
        FROM {lti_types}
    WHERE baseurl = :baseurl
    AND state = :state";
    return $DB->get_records_sql($query, array(
        'baseurl' => local_mediacore_fetch_lti_url(),
        'state' => MEDIACORE_LOCAL_LTI_TOOL_STATE_CONFIGURED,
    ));
}

/**
 * Fetch lti tool ids by course id from config
 * @param int $cid
 * @return object
 */
function local_mediacore_fetch_lti_tool_ids_by_course_id($cid) {
    global $DB;
    return $DB->get_record('config_plugins', array(
        'plugin' => MEDIACORE_LOCAL_COURSELTI_SETTING_NAME,
        'name' => (string)$cid,
    ));
}

/**
 * Fetch lti tool types by course id
 * @param int $cid
 * @return object
 */
function local_mediacore_fetch_lti_tools_by_course_id($cid) {
    global $DB;

    $config = local_mediacore_fetch_lti_tool_ids_by_course_id($cid);
    if (empty($config) || empty($config->value)) {
        return FALSE;
    }

    $or_where_arr = array();
    $tool_ids = explode(',',$config->value);
    foreach ($tool_ids as $id) {
        array_push($or_where_arr, "id=$id");
    }
    $lti_types = $DB->get_records_select('lti_types', implode(' OR ', $or_where_arr));
    if (!empty($lti_types)) {
        return $lti_types;
    }

    return FALSE;
}

/**
 * Build the request parameters for the lti request
 * @param object $lti_type
 * @param array $type_config
 * @param object $course;
 * @return array
 */
function local_mediacore_lti_build_request($lti_type, $type_config, $course) {
    global $USER, $CFG;

    $request_params = array(
        'resource_link_id' => $lti_type->id,
        'resource_link_title' => $lti_type->name,
        'user_id' => $USER->id,
        'roles' => lti_get_ims_role($USER, 0, $course->id),
        'context_id' => $course->id,
        'context_label' => $course->shortname,
        'context_title' => $course->fullname,
        'launch_presentation_locale' => current_language(),
    );

    // Send user's name and email data if appropriate
    if ($type_config['sendname'] == LTI_SETTING_ALWAYS) {
        $request_params['lis_person_name_given'] =  $USER->firstname;
        $request_params['lis_person_name_family'] =  $USER->lastname;
        $request_params['lis_person_name_full'] =  $USER->firstname." ".$USER->lastname;
        $request_params['lis_person_contact_email_primary'] = $USER->email;
    }

    // Make sure we let the tool know what LMS they are being called from
    $request_params["ext_lms"] = "moodle-2";
    $request_params['tool_consumer_info_product_family_code'] = 'moodle';
    $request_params['tool_consumer_info_version'] = (string)$CFG->version;
    // Add oauth_callback to be compliant with the 1.0A spec
    $request_params['oauth_callback'] = 'about:blank';
    $request_params['lti_version'] = 'LTI-1p0';
    $request_params['lti_message_type'] = 'basic-lti-launch-request';
    return $request_params;
}

