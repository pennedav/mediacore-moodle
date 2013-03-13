/**
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore's tinymce plugin
 * Compatible with Moodle v2.3 only
 *
 * @package    tinymce
 * @subpackage mediacore
 * @copyright  2012 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

(function (tinymce) {

  tinymce.create('tinymce.plugins.mediacore', {

    init : function (ed, url) {

      // Register commands
      ed.addCommand('mceMoodleMediaCore', function () {

        // relies on mediacore config in tinymce/lib.php
        var cidStr = '', cid;
        if (cid = ed.getParam('course_id')) {
          cidStr += '?course_id=' + cid;
        }
        ed.windowManager.open({
          file : url + '/mediacore.php' + cidStr,
          height: 684,
          width: 460,
          inline : 1,
          popup_css : false
        }, {
          plugin_url : url
        });
      });

      // Register buttons
      ed.addButton('mediacore', {
        title : 'MediaCore',
        cmd : 'mceMoodleMediaCore',
        image : url + "/mcore-icon.png"
      });
    },

    getInfo : function () {
      return {
        longname : 'MediaCore',
        version : '1'
      };
    }
  });

  // Register plugin
  tinymce.PluginManager.add('mediacore', tinymce.plugins.mediacore);
})(tinymce);
