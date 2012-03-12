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
  //var transformed_text = "";
  
  // N'appliquer le plugin que si une partie du texte a été sélectionnée
  if (selected_text != '') {
    
    // S'il y a des minuscules et/ou des majuscules, on passe en majuscule
    if (/[:lower:,:upper:][:lower:,:upper:]+/.test(selected_text) || selected_text == selected_text.toLowerCase()) {
      var style = {
      element: 'span',
      attributes: {
          'style': 'text-transform: uppercase'
        }
      };
    }
    // Passage en minuscule
    else {
      var style = {
        element: 'span',
        attributes: {
          'style': 'text-transform: lowercase'
        }
      };
    }
    
    var styleCK = new CKEDITOR.style(style);
    editor.addCommand('subscript', new CKEDITOR.styleCommand(styleCK));
    editor.execCommand("subscript");
  }
}
