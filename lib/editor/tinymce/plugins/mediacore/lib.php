<?php
/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore's tinymce plugin
 *
 * @package    local
 * @subpackage tinymce_mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die('Invalid access');

require_once($CFG->dirroot . '/local/mediacore/lib.php');

/**
 * Plugin for MediaCore media
 *
 * @package tinymce_mediacore
 * @copyright 2012 MediaCore Technologies Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_mediacore extends editor_tinymce_plugin {

    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('mediacore');

    protected function update_init_params(array &$params, context $context,
        array $options = null) {

            global $CFG, $COURSE;

            // If mediacore filter is disabled, do not add button.
            $filters = filter_get_active_in_context($context);
            if (!array_key_exists('filter/mediacore', $filters)) {
                return;
            }

            $mcore_client = new mediacore_client();
            $params['chooser_js_url'] = $mcore_client->get_chooser_js_url();
            $params['chooser_params'] = array();
            if (isset($COURSE->id)) {
                $params['chooser_params'] = array(
                        'context_id'    => urlencode($COURSE->id),
                        'context_title' => urlencode($COURSE->fullname),
                        'context_label' => urlencode($COURSE->shortname),
                    );
            }

            // Add button after emoticon button in advancedbuttons3.
            $added = $this->add_button_after($params, 3, 'mediacore', 'moodleemoticon', false);

            // Note: We know that the emoticon button has already been added, if it
            // exists, because I set the sort order higher for this. So, if no
            // emoticon, add after 'image'.
            if (!$added) {
                $this->add_button_after($params, 3, 'mediacore', 'image');
            }

            // Add JS file, which uses default name.
            $this->add_js_plugin($params);
        }
}
