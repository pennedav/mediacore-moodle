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
(function() {

     tinymce.create('tinymce.plugins.MediaCoreChooserPlugin', {

        init : function(ed, url) {
            var t = this;
            t.editor = ed;
            t.url = url;

            var e = document.createElement("script");
            e.src = ed.getParam('chooser_js_url');
            e.type="text/javascript";
            document.getElementsByTagName("head")[0].appendChild(e);

            var params = {
                'host': ed.getParam('host'),
                'chooser_query_str': ed.getParam('chooser_query_str', undefined),
                'ieframe_query_str': ed.getParam('ieframe_query_str', undefined)
            };

            ed.addCommand('mceMoodleMediaCore', function() {
                if (!window.mediacore) {
                    ed.windowManager.alert(
                        'MediaCore Tinymce Load error'
                    );
                    return;
                }
                if (!t.chooser) {
                    t.chooser = mediacore.chooser.init(params);
                    t.chooser.on('media', function(media) {
                        var imgElem = t.editor.dom.createHTML('img', {
                            src:  media.thumb_url,
                            width: 195,
                            height: 110,
                            alt: media.title,
                            title: media.title
                        });
                        var aElem = t.editor.dom.createHTML('a', {href : media.public_url}, imgElem);
                        t.editor.execCommand('mceInsertContent', false, aElem);
                    });
                    t.chooser.on('error', function(err) {
                        throw err;
                    });
                }
                t.chooser.open();
            });

            ed.addButton('mediacore', {
                title : 'MediaCore',
                cmd : 'mceMoodleMediaCore',
                image : t.url + '/icon.png'
            });

        },

        getInfo : function() {
            return {
                longname : 'MediaCore Chooser',
                author : 'MediaCore <info@mediacore.com>',
                authorurl: 'http://mediacore.com',
                version : "2.0"
            };
        }

    });

    tinymce.PluginManager.add('mediacore', tinymce.plugins.MediaCoreChooserPlugin, []);
})();
