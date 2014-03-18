/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbprintPDF',{
  requires: ['dialog'],
  init: function(editor){
    editor.addCommand('mbprintPDF', {exec: mbprintPDF_onclick});
    editor.ui.addButton('mbprintPDF', {label:'Imprimer en PDF', command:'mbprintPDF',
    	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png' });
  }
});

function mbprintPDF_onclick(editor) {
  if (!Thumb.doc_lock) {
    editor.getCommand('mbprintPDF').setState(CKEDITOR.TRISTATE_DISABLED);
  }
  window.parent.Url.ping({onComplete: function() {
    if (Thumb.mode == "doc") {
      if (Thumb.doc_lock) {
        streamPDF(editor);
      }
      else {
        // Mise à jour de la date d'impression
        $V(getForm("editFrm").date_print, "now");
        submitCompteRendu(function() {
          streamPDF(editor);
          editor.getCommand('mbprintPDF').setState(CKEDITOR.TRISTATE_OFF);
        });
      }
    }
    else {
      streamPDF(editor);
      editor.getCommand('mbprintPDF').setState(CKEDITOR.TRISTATE_OFF);
    }
  } });
}

function streamPDF(editor) {
  if (pdf_thumbnails && window.parent.Prototype.Browser.IE) {
    restoreStyle();
  }
  var content = editor.getData();
  if (pdf_thumbnails && Prototype.Browser.IE) {
    save_style = deleteStyle();
  }
  var form = getForm("download-pdf-form");
  form.elements.content.value = encodeURIComponent(content);
  form.onsubmit();
}