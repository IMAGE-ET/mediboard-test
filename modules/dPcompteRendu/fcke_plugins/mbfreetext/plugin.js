/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Adding free text area in the editor 
 */
 
CKEDITOR.plugins.add('mbfreetext',{
  requires: ['dialog'],
  init: function(editor) {
  CKEDITOR.dialog.add('mbfreetext_dialog', function() {
    return {
      title : 'Insérer une zone de texte libre',
      minWidth : 420,
      minHeight : 150,
      contents :
      [
        {
          id : 'iframe',
          label : 'Insertion de zone de texte libre',
          elements :
            [
              {
                type : 'html',
                html: '<iframe src="modules/dPcompteRendu/fcke_plugins/mbfreetext/dialogs/insert_area.html" style="width: 100%; height: 100%"></iframe>'
              }
            ]
        }
      ]
     };
   });
   editor.addCommand('mbfreetext', {exec: mbfreetext_onclick});
   editor.ui.addButton('mbfreetext', {label:'Texte libre', command:'mbfreetext',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mbfreetext/images/mbfreetext.png' });
  }
});

function mbfreetext_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mbfreetext_dialog');
}
