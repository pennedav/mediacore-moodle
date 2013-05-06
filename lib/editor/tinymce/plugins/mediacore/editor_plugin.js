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

    function loadScript(url) {
        var script = document.createElement('script');
        script.src = url;
        (document.body || document.head || document.documentElement).appendChild(script);
    }

     tinymce.create('tinymce.plugins.MediaCoreChooserPlugin', {
        init : function(ed, pluginUrl) {
            var t = this;
            t.editor = ed;
            t.url = pluginUrl;

            loadScript(ed.getParam('chooser_js_url'));
            var params = {
                'host': ed.getParam('host'),
                'scheme': ed.getParam('scheme', 'http'),
                'chooser_query_str': ed.getParam('chooser_query_str', undefined),
                'ieframe_query_str': ed.getParam('ieframe_query_str', undefined)
            };

            ed.addCommand('mceMediaCoreChooser', function() {
                if (!window.mediacore) {
                    ed.windowManager.alert(
                        ed.getLang('mediacore.loaderror')
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
                title : 'MediaCore Chooser',
                image : t.url + '/img/icon.png',
                cmd : 'mceMediaCoreChooser'});

        },

        getInfo : function() {
            return {
                longname : 'MediaCore Chooser',
                author : 'MediaCore <info@mediacore.com>',
                authorurl: 'http://mediacore.com',
                version : "2.0b"
            };
        }

    });

    tinymce.PluginManager.add('mediacore', tinymce.plugins.MediaCoreChooserPlugin, []);
})();
