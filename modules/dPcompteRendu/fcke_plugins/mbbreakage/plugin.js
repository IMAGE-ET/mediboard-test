/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Uppercase / Lowercase a selection
 */

CKEDITOR.plugins.add('mbbreakage',{
  requires: ['iframedialog'],
  init:function(editor) {
    editor.addCommand('mbbreakage', {exec: mbbreakage_onclick});
    editor.ui.addButton('mbbreakage', {label:'Majuscule / Minuscule', command:'mbbreakage',
         icon:'../../modules/dPcompteRendu/fcke_plugins/mbbreakage/images/icon.png' });
  }
});

function mbbreakage_onclick(editor) {
  var selection = editor.getSelection();
  var selected_text = selection.getSelectedText();
  
  // N'appliquer le plugin que si une partie du texte a été sélectionnée
  if (selected_text != '') {
    var ranges = selection.getRanges();
    var rangeIterator = ranges.createIterator();
    var range = null;
    var uppercase = 0;
    
    // S'il y a des minuscules et/ou des majuscules, on passe en majuscule
    if (selected_text == selected_text.toLowerCase()) {
      uppercase = 1;
    }
    
    while ((range = rangeIterator.getNextRange(1))) {
      var walker = new CKEDITOR.dom.walker( range );
      var node = null;
      while ( ( node = walker.next() ) ) {
        if (uppercase) {
          node.$.nodeValue = node.$.nodeValue.toUpperCase();
        }
        else {
          node.$.nodeValue = node.$.nodeValue.toLowerCase();
        }
      }
    }
  }
}
