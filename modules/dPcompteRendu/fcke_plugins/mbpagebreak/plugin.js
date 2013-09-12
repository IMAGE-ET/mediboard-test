/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Romain OLLIVIER
 *
 * Mediboard additional page-break button plugin for FCKeditor
 */

 
CKEDITOR.plugins.add('mbpagebreak',{
  requires: ['dialog'],
  init: function(editor) {
    editor.addCommand('mbpagebreak', {exec: mbpagebreak_onclick});
    editor.ui.addButton('mbpagebreak', {label:'Saut de page', command:'mbpagebreak',
      icon:'../../modules/dPcompteRendu/fcke_plugins/mbpagebreak/images/mbpagebreak.gif' });
  }
});

function mbpagebreak_onclick(editor) {
  editor.insertHtml("<hr class='pagebreak' />");
  editor.fire("key", []);
}
