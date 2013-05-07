/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Activate or deactivate autocapitalisation
 */

CKEDITOR.plugins.add('mbcap',{
  requires: ['iframedialog'],
  init:function(editor) {
    editor.addCommand('mbcap', {exec: mbcap_onclick});
    editor.ui.addButton('mbcap', {label:'Majuscule automatique en début de phrase', command:'mbcap',
         icon:'../../modules/dPcompteRendu/fcke_plugins/mbcap/images/icon.png' });
    editor.on("instanceReady", function() {
      // On regarde la préférence
      if (window.parent.Preferences.auto_capitalize == "1") {
        mbcap_onclick(editor);
      }
    });
  }
});

function mbcap_onclick(editor) {
  var command = editor.getCommand('mbcap');
  
  if (command.state == CKEDITOR.TRISTATE_ON) {
    editor.document.getBody().removeListener('keydown', autoCap);
    command.setState(CKEDITOR.TRISTATE_OFF);
  }
  else {
    editor.document.getBody().on('keydown', autoCap);
    command.setState(CKEDITOR.TRISTATE_ON);
  }
}

function insertUpperCase(editor, event, keystroke) {
  // insert 'A' instead of 'a' (example)
  editor.insertText(String.fromCharCode(keystroke).toUpperCase());
  event.data.preventDefault();
}

function autoCap(event) {
  var editor = window.parent.CKEDITOR.instances.htmlarea;
  var keystroke = event.data.getKeystroke();

  if (keystroke < 65 || keystroke > 90) {
    return;
  }

  var range, walker, selection, native, chars;
  selection = editor.getSelection();
  range = selection.getRanges()[0];
  range.setStartAt(editor.document.getBody(), CKEDITOR.POSITION_AFTER_START);
  walker = new CKEDITOR.dom.walker(range);

  var node = walker.previous();

  if (!node) {
    return insertUpperCase(editor, event, keystroke);
  }

  native = selection.getNative();

  if (!Object.isUndefined(native.focusNode) && native.focusNode.data) {
    chars = native.focusNode.data.substr(native.anchorOffset-2, +2);
  }

  if (
  /* Commence par un retour chariot ou une ligne verticale */
    node.$.nodeName == "BR" ||
    node.$.data == ""       ||
    (node.$.data && node.$.data.length == 0) ||
    (!Object.isUndefined(node.$.data) && window.parent.Prototype.Browser.IE && /[\.\?!]\s/.test(node.$.data.substr(-2))) ||
    /(<br|<hr)/.test(node.$.data) ||
      (native.focusNode && native.focusNode.length == 0) ||
      /* Les 2 derniers caractères sont :
       - un point ou
       - un point d'exclamation ou
       - un point d'interrogation
       et un espace*/
      /[\.\?!]\s/.test(chars)) {
    insertUpperCase(editor, event, keystroke)
  }
}