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
  if (window.parent.Thumb.mode == "doc") {
    window.parent.submitCompteRendu(function() {
      streamPDF(editor);
    });
  }
  else {
    streamPDF(editor);
  }
}

function streamPDF(editor) {
  restoreStyle();
  var content = editor.getData();
  deleteStyle();
  var form = window.parent.document.forms["download-pdf-form"];
  form.elements.content.value = encodeURIComponent(content);
  form.onsubmit();
}