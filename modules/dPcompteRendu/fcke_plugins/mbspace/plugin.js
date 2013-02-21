/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Thomas Despoix
 *
 */

CKEDITOR.plugins.add('mbspace',{
  requires: ['iframedialog'],
  init:function(editor) {
    editor.addCommand('mbspace', {exec: mbspace_onclick});
    editor.ui.addButton('mbspace', {label:'Espace insécable', command:'mbspace',
      icon:'../../modules/dPcompteRendu/fcke_plugins/mbspace/images/icon.png' });
  }
});

function mbspace_onclick(editor) {
  editor.focus();
  if (CKEDITOR.env.gecko) {
    insertSpecialChar(editor, '&nbsp;');
  }
  else {
    editor.insertHtml('&nbsp;');
  }
  return true;
}

function insertSpecialChar(editor, specialChar) {
  var selection = CKEDITOR.instances.htmlarea.getSelection(),
    ranges    = selection.getRanges(),
    range, textNode;

  for ( var i = 0, len = ranges.length ; i < len ; i++ ) {
    range = ranges[ i ];
    range.deleteContents();
    textNode = CKEDITOR.dom.element.createFromHtml( specialChar );
    range.insertNode( textNode );
  }

  range.moveToPosition( textNode, CKEDITOR.POSITION_AFTER_END );
  range.select();
}