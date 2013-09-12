/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Thomas Despoix
 *
 */

CKEDITOR.plugins.add('mbfooter',{
  requires: ['dialog'],
  init:function(editor) {
    editor.addCommand('mbfooter', {exec: mbfooter_onclick});
    editor.ui.addButton('mbfooter', {label:'Pied de page', command:'mbfooter',
      icon:'../../modules/dPcompteRendu/fcke_plugins/mbfooter/images/icon.gif' });
    editor.on("instanceReady", function() {
	    if (window.parent.document.getElementById('htmlarea').innerHTML.indexOf("footer") == -1) {
	      editor.getCommand('mbfooter').setState(CKEDITOR.TRISTATE_DISABLED);
	    }
    });
  }
});

function mbfooter_onclick(editor) {
  var oFooter = editor.document.getById("footer");
  if (!oFooter) return;
  if (oFooter.$.style.display == "block") {
    oFooter.$.style.display = "none";
    editor.getCommand('mbfooter').setState(CKEDITOR.TRISTATE_OFF);
    return;
  }
  oFooter.$.style.display = "block";
   editor.getCommand('mbfooter').setState(CKEDITOR.TRISTATE_ON);
}
