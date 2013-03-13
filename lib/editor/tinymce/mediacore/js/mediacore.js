tinyMCEPopup.requireLangPack();

var MediaCorePopup = {

  /**
   */
  init: function(ed) {
    tinyMCEPopup.resizeToInnerSize();
  },

  /**
   */
  insert: function(file, title, typeId) {
    var ed = tinyMCEPopup.editor, dom = ed.dom;

    var href = this._addUrlParamStr(file, 'type_id=' + typeId);
    tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {
      href : href
    }, title));

    tinyMCEPopup.close();
  },

  /**
   */
  _addUrlParamStr: function(url, paramStr) {

    var params,
        queryStr = url.split('?'),
        newQueryStr = '';

    if (!queryStr.length) {
      newQueryStr = paramStr;
    } else {
      newQueryStr += queryStr[1] + '&' + paramStr;
    }

    return url + '?' + paramStr;
  }
};

tinyMCEPopup.onInit.add(MediaCorePopup.init, MediaCorePopup);
