/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbthumbs',{
  requires: ['iframedialog'],
  init:function(editor){ 
   editor.addCommand('mbthumbs', {exec: mbthumbs_onclick});
   editor.ui.addButton('mbthumbs', {label:'Rafraichir les vignettes', command:'mbthumbs',
     icon:'../../style/mediboard/images/buttons/change.png' });
  }
});

function mbthumbs_onclick(editor) {
  editor.on("key", loadOld);
  window.parent.Thumb.refreshThumbs();
}
