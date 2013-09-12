/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbthumbs', {
  requires: ['dialog'],
  init: function(editor) {
   editor.addCommand('mbthumbs', {exec: mbthumbs_onclick});
   editor.ui.addButton('mbthumbs', {label: 'Rafraichir les vignettes', command: 'mbthumbs',
     icon:'../../style/mediboard/images/buttons/change.png' });
   editor.addCommand('mbhidethumbs', {exec: mbhidethumbs_onclick});
    editor.ui.addButton('mbhidethumbs', {label: 'Afficher/Cacher les vignettes', command: 'mbhidethumbs',
      icon:'../../style/mediboard/images/buttons/hslip.png' });
  }
});

function mbthumbs_onclick(editor) {
  editor.on("key", loadOld);
  window.parent.Thumb.refreshThumbs();
}

function mbhidethumbs_onclick(editor) {
  var command = editor.getCommand('mbhidethumbs');
  if (command.state == CKEDITOR.TRISTATE_ON) {
    command.setState(CKEDITOR.TRISTATE_OFF);
  }
  else {
    command.setState(CKEDITOR.TRISTATE_ON);
  }

  window.parent.Thumb.choixAffiche();
}
