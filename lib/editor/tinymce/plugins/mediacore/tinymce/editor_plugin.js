/**
 * @author MediaCore <info@mediacore.com>
 */

(function() {
    function loadScript(url) {
        var script = document.createElement('script');
        script.src = url;
        (document.body || document.head || document.documentElement).appendChild(script);
    }

    tinymce.PluginManager.requireLangPack('mediacore');

    tinymce.create('tinymce.plugins.MediaCoreInsertPlugin', {
        init : function(ed, pluginUrl) {
            var t = this;
            t.editor = ed;
            t.url = pluginUrl;

            var host = ed.getParam('host');
            loadScript(host + '/api/chooser.js');

            ed.addCommand('mceMediaCoreInsert', function() {
                if (!window.mediacore) {
                    ed.windowManager.alert(
                        tinyMCEPopup.getLang('mediacore.loaderror')
                    );
                    return;
                }
                if (!t.chooser) {
                    t.chooser = mediacore.chooser.init();
                    t.chooser.on('media', function(media) {
                        var thumb = media.thumbs.s;
                        var img = t.editor.dom.createHTML('img', {
                            src: thumb.url,
                            width: thumb.x,
                            height: thumb.y,
                            alt: media.title,
                            title: media.title
                        });
                        var el = t.editor.dom.createHTML('a', {href : media.url}, img);
                        t.editor.execCommand('mceInsertContent', false, el);
                    });
                    t.chooser.on('error', function(err) {
                        throw err;
                    });
                }
                t.chooser.open();
            });

            ed.addButton('mediacore', {
                title : 'mediacore.desc',
                image : t.url + '/img/icon.png',
                cmd : 'mceMediaCoreInsert'});

        },

        getInfo : function() {
            return {
                longname : 'MediaCore Media',
                author : 'MediaCore <info@mediacore.com>',
                authorurl: 'http://mediacore.com',
                version : "1.0"
            };
        }

    });

    tinymce.PluginManager.add('mediacore', tinymce.plugins.MediaCoreInsertPlugin);
})();
