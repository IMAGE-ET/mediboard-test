/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbprintPDF',{
  requires: ['iframedialog'],
  init:function(editor){ 
   editor.addCommand('mbprintPDF', {exec: mbprintPDF_onclick});
   editor.ui.addButton('mbprintPDF', {label:'Imprimer en PDF', command:'mbprintPDF',
   	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png' });
  }
});

function mbprintPDF_onclick(editor) {
  window.parent.Url.ping({onComplete: function() {
    if (window.parent.Thumb.mode == "doc") {
      // Mise à jour de la date d'impression
      window.parent.$V(window.parent.getForm("editFrm").date_print, "now");
      window.parent.submitCompteRendu(function() {
        streamPDF(editor);
      });
    }
    else {
      streamPDF(editor);
    }
  } });
}

function streamPDF(editor) {
  if (window.parent.pdf_thumbnails && window.parent.Prototype.Browser.IE) {
    window.parent.restoreStyle();
  }
  var content = editor.getData();
  if (window.parent.pdf_thumbnails && window.parent.Prototype.Browser.IE) {
    window.parent.save_style = window.parent.deleteStyle();
  }
  var form = window.parent.document.forms["download-pdf-form"];
  form.elements.content.value = encodeURIComponent(content);
  form.onsubmit();
}