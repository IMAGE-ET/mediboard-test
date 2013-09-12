/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Activate or deactivate helper autocompletion
 */

CKEDITOR.plugins.add('mbreplace', {
  init: function(editor) {
    editor.addCommand('mbreplace', {exec: mbreplace_onclick});
    editor.ui.addButton('mbreplace', {label:'Autocomplétion d\'aide à la saisie', command:'mbreplace',
         icon:'../../modules/dPcompteRendu/fcke_plugins/mbreplace/images/mbreplace.png' });
    editor.on("instanceReady", function() {
      // On regarde la préférence
      if (window.parent.Preferences.auto_replacehelper == "1") {
        mbreplace_onclick(editor);
      }
    });
  }
});

function mbreplace_onclick(editor) {
  var command = editor.getCommand('mbreplace');
  command.setState(command.state == CKEDITOR.TRISTATE_ON ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_ON);
}
