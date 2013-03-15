<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore's local plugin settings
 *
 * @package    local
 * @subpackage mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die('Invalid access');

if ($hassiteconfig) {

    require_once($CFG->dirroot . '/local/mediacore/locallib.php');

    $settings = new admin_settingpage('local_mediacore', get_string('pluginname', 'local_mediacore'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading('setting_url_heading',
        get_string('setting_url_heading_title', 'local_mediacore'),
        '', 'local_mediacore'));

    $admin_setting = new admin_setting_configtext('local_mediacore_courselti/mediacore_url',
        get_string('setting_url_label', 'local_mediacore'),
        get_string('setting_url_desc', 'local_mediacore'),
        'http://demo.mediacore.tv', PARAM_TEXT);
    $admin_setting->plugin = MEDIACORE_LOCAL_COURSELTI_SETTING_NAME;
    $settings->add($admin_setting);

    $courses = local_mediacore_fetch_courses();
    $lti_tools = local_mediacore_fetch_lti_tools();
    if (!empty($courses) && !empty($lti_tools)) {
        $lti_tool_choices = array();
        foreach ($lti_tools as $t) {
            $lti_tool_choices[$t->id] = $t->name;
        }
        $settings->add(new admin_setting_heading('setting_courses_heading',
            get_string('setting_courses_heading_title', 'local_mediacore'),
            '', 'local_mediacore'));
        foreach ($courses as $c) {
            $header_str = get_string('setting_course_fullname_prefix', 'local_mediacore');
            $admin_setting = new admin_setting_configmulticheckbox('local_mediacore_courselti/' . $c->id,
                "<strong>$c->fullname</strong>",
                get_string('setting_course_lti_desc', 'local_mediacore'), NULL, $lti_tool_choices);
            $admin_setting->plugin = MEDIACORE_LOCAL_COURSELTI_SETTING_NAME;
            $settings->add($admin_setting);
        }
    }
}
